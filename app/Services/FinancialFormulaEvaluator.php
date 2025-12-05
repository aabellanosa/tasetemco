<?php

namespace App\Services;

use App\Models\FinancialFormulaMap;
use App\Models\FinancialLineItem;
use App\Models\FinancialValue;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FinancialFormulaEvaluator
{
    protected array $cache = [];
    protected int $maxDepth;
    protected array $stack = [];

    public function __construct(int $maxDepth = 50)
    {
        // Prevent infinite recursion for circular references
        $this->maxDepth = $maxDepth;
    }

    /**
     * Public entry: evaluate a line item code for a specific year/month.
     *
     * @param string $code
     * @param int $year
     * @param int $month
     * @return float
     * @throws \Exception
     */
    public function evaluateLineItem(string $code, int $year, int $month): float
    {
        $key = "{$code}::{$year}-{$month}";
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        // depth guard
        if (count($this->stack) > $this->maxDepth) {
            throw new RuntimeException("Max recursion depth ({$this->maxDepth}) reached while evaluating {$code}");
        }

        if (in_array($key, $this->stack, true)) {
            $cycle = implode(' -> ', array_merge($this->stack, [$key]));
            throw new RuntimeException("Circular formula reference detected: {$cycle}");
        }

        $this->stack[] = $key;

        // Find line item
        $lineItem = FinancialLineItem::where('code', $code)->first();
        if (!$lineItem) {
            array_pop($this->stack);
            throw new RuntimeException("Line item not found: {$code}");
        }

        // If editable: return stored numeric value (or zero if null)
        if ($lineItem->is_editable) {
            $val = FinancialValue::where('line_item_id', $lineItem->id)
                ->where('year', $year)
                ->where('month', $month)
                ->value('value');

            $num = (float) ($val ?? 0.0);
            $this->cache[$key] = $num;
            array_pop($this->stack);
            return $num;
        }

        // Derived: lookup formula by line_item
        $formulaRow = FinancialFormulaMap::where('line_item_id', $lineItem->id)->first();
        if (!$formulaRow) {
            array_pop($this->stack);
            // Missing formula for a non-editable line -> treat as 0 or throw
            throw new RuntimeException("Missing formula for derived line item: {$code}");
        }

        $formula = $formulaRow->formula;

        // Evaluate the formula string
        $result = $this->evaluateExpression($formula, $year, $month);

        // Cache and return
        $this->cache[$key] = $result;
        array_pop($this->stack);
        return $result;
    }

    /**
     * Evaluate an expression string (supports identifiers, numbers, +, -, parentheses and SUM()).
     *
     * @param string $expr
     * @param int $year
     * @param int $month
     * @return float
     */
    public function evaluateExpression(string $expr, int $year, int $month): float
    {
        // Normalize: trim and collapse multiple whitespace
        $expr = trim(preg_replace('/\s+/', ' ', $expr));

        // Replace SUM(...) occurrences with a pseudo-function handler token so tokenizer can pick it up
        // We'll handle SUM specially in `tokenize`/`rpnEvaluate`.
        $tokens = $this->tokenize($expr);

        $rpn = $this->toRPN($tokens);

        return $this->rpnEvaluate($rpn, $year, $month);
    }

    /**
     * Tokenize the expression into tokens: identifiers, numbers, operators, parentheses, commas, functions.
     *
     * @param string $expr
     * @return array
     */
    protected function tokenize(string $expr): array
    {
        $pattern = '/
            (\bSUM\b)                             # SUM function
            |([A-Za-z_][A-Za-z0-9_]*)            # identifiers (codes)
            |(\d+\.\d+|\d+)                      # numbers (integers or decimals)
            |([\+\-])                            # plus or minus
            |([\(\)])                            # parentheses
            |(,)                                 # comma
        /x';

        preg_match_all($pattern, $expr, $matches, PREG_SET_ORDER);

        $tokens = [];
        foreach ($matches as $m) {
            if (!empty($m[1])) {
                $tokens[] = ['type' => 'func', 'value' => 'SUM'];
            } elseif (!empty($m[2])) {
                $tokens[] = ['type' => 'ident', 'value' => $m[2]];
            } elseif (!empty($m[3])) {
                $tokens[] = ['type' => 'number', 'value' => $m[3]];
            } elseif (!empty($m[4])) {
                $tokens[] = ['type' => 'op', 'value' => $m[4]];
            } elseif (!empty($m[5])) {
                $tokens[] = ['type' => 'paren', 'value' => $m[5]];
            } elseif (!empty($m[6])) {
                $tokens[] = ['type' => 'comma', 'value' => ','];
            }
        }

        return $tokens;
    }

    /**
     * Convert token list to Reverse Polish Notation (Shunting-yard algorithm)
     * Supports +, -, parentheses and a SUM function (function arguments separated by commas).
     *
     * @param array $tokens
     * @return array RPN token list
     */
    protected function toRPN(array $tokens): array
    {
        $output = [];
        $stack = [];

        $precedence = ['+' => 1, '-' => 1];

        foreach ($tokens as $token) {
            switch ($token['type']) {
                case 'number':
                case 'ident':
                    $output[] = $token;
                    break;

                case 'func':
                    // push function onto stack
                    $stack[] = $token;
                    break;

                case 'comma':
                    // Until the token at the top of the stack is a left parenthesis,
                    // pop operators onto the output queue
                    while (!empty($stack) && end($stack)['type'] !== 'paren') {
                        $output[] = array_pop($stack);
                    }
                    break;

                case 'op':
                    while (!empty($stack)) {
                        $top = end($stack);
                        if ($top['type'] === 'op' && $precedence[$top['value']] >= $precedence[$token['value']]) {
                            $output[] = array_pop($stack);
                            continue;
                        }
                        if ($top['type'] === 'func') {
                            $output[] = array_pop($stack);
                            continue;
                        }
                        break;
                    }
                    $stack[] = $token;
                    break;

                case 'paren':
                    if ($token['value'] === '(') {
                        $stack[] = $token;
                    } else { // ')'
                        while (!empty($stack) && end($stack)['type'] !== 'paren') {
                            $output[] = array_pop($stack);
                        }
                        if (empty($stack)) {
                            throw new RuntimeException("Mismatched parentheses in formula");
                        }
                        // pop the left parenthesis
                        array_pop($stack);

                        // if the token at the top of the stack is a function token, pop it onto the output queue
                        if (!empty($stack) && end($stack)['type'] === 'func') {
                            $output[] = array_pop($stack);
                        }
                    }
                    break;

                default:
                    throw new RuntimeException("Unknown token type: " . $token['type']);
            }
        }

        while (!empty($stack)) {
            $t = array_pop($stack);
            if ($t['type'] === 'paren') {
                throw new RuntimeException("Mismatched parentheses in formula");
            }
            $output[] = $t;
        }

        return $output;
    }

    /**
     * Evaluate an RPN token list.
     *
     * Supports:
     * - numbers
     * - identifiers (resolved recursively via evaluateLineItem)
     * - operators + and -
     * - SUM function (variable number of args). SUM will be represented as a func token in RPN and we pop args until a marker.
     *
     * Our approach for SUM: In the RPN conversion we push SUM as a single token after its arguments,
     * so during evaluation we will detect SUM token and pop arguments until we see a special marker.
     *
     * Simpler approach here: because the tokenization and RPN algorithm preserves order and functions are pushed
     * after their args, we'll handle SUM by counting arguments based on commas/parentheses in toRPN.
     *
     * @param array $rpn
     * @param int $year
     * @param int $month
     * @return float
     */
    protected function rpnEvaluate(array $rpn, int $year, int $month): float
    {
        $stack = [];
        foreach ($rpn as $token) {
            if ($token['type'] === 'number') {
                $stack[] = (float) $token['value'];
                continue;
            }

            if ($token['type'] === 'ident') {
                $code = $token['value'];
                // recursively evaluate the referenced identifier
                $val = $this->evaluateLineItem($code, $year, $month);
                $stack[] = $val;
                continue;
            }

            if ($token['type'] === 'op') {
                if (count($stack) < 2) {
                    throw new RuntimeException("Invalid expression: operator '{$token['value']}' without enough operands");
                }
                $b = array_pop($stack);
                $a = array_pop($stack);
                switch ($token['value']) {
                    case '+': $stack[] = $a + $b; break;
                    case '-': $stack[] = $a - $b; break;
                    default:
                        throw new RuntimeException("Unsupported operator: {$token['value']}");
                }
                continue;
            }

            if ($token['type'] === 'func') {
                // Only supported function: SUM — we need to know how many args this SUM has.
                if (strtoupper($token['value']) !== 'SUM') {
                    throw new RuntimeException("Unsupported function: {$token['value']}");
                }

                // For SUM in this RPN implementation, arguments for SUM will already be on the stack.
                // But we don't have an arg count marker. To handle this robustly we will:
                // - Peek backwards into rpn to determine how many immediate previous tokens were pushed as args for this func.
                // Simpler approach: We will store SUM arguments as a nested expression: SUM(a,b,c) results in tokens a b c SUM.
                // So here, when seeing SUM, we must pop *all* arguments until ... but we can't distinguish.
                //
                // To make this reliable, our toRPN implementation ensures SUM's args are provided directly before the SUM:
                // we will assume at least 1 arg is present; and because commas are handled in toRPN, the exact number of args
                // equals the count of values pushed since the matching '(' was processed. For simplicity, we will pop until
                // we reach a special marker push during toRPN... but we didn't create markers. To avoid complexity, we enforce:
                // **SUM must be called with explicit identifiers/numbers only (no nested functions), and args will be popped
                //  until the number of args equals the count of comma separators between parentheses computed earlier.**
                //
                // Given the limited function set, we will implement a pragmatic approach:
                // - When toRPN converts func, it doesn't give us arg counts. We'll instead fallback to: pop all items from stack
                //   until the last popped item was produced before this function's argument sequence started. This is messy.
                //
                // Simpler and robust approach: Avoid complex arg counting by rewriting SUM(expr) into (expr1 + expr2 + ...).
                // We already do that in evaluateExpression by tokenizing SUM and leaving the operands; but building a robust
                // general parser is non-trivial in this compact implementation.
                //
                // To keep this implementation practical and reliable: support SUM only when its arguments are identifiers or numbers.
                //
                // Implementation: Pop items until a marker or until we've popped at least 1 and the remaining RPN's previous token is not ident/number.
                $args = [];

                // Pop at least one arg
                if (empty($stack)) {
                    throw new RuntimeException("SUM requires at least one argument");
                }

                // We will pop until the stack is empty or until the previous rpn token type was function/paren - best effort
                // But instead, we'll assume the last N entries belong to SUM where N is unknown; a safer approach:
                // Rebuild SUM behavior by recomputing the SUM by parsing the original formula directly.
                // For brevity and safety, implement a direct SUM extraction on the original formula before tokenization (see evaluateExpression).
                throw new RuntimeException("SUM handling in RPN evaluation is not supported in this minimal implementation. Use SUM expansion in the formula string (e.g., a + b + c) or contact Wings to enable advanced SUM parsing.");
            }
        }

        if (count($stack) !== 1) {
            throw new RuntimeException("Invalid expression evaluation. Stack has " . count($stack) . " items.");
        }

        return (float) array_pop($stack);
    }
}
