<?php

namespace App\Services;

use App\Models\FinancialFormulaMap;
use App\Models\FinancialLineItem;
use App\Models\FinancialValue;
use Illuminate\Support\Collection;
use RuntimeException;

class FinancialFormulaEvaluator
{
    /**
     * Evaluate all formulas and return a map keyed by code.
     * Accepts optional $overrides: ['code' => numeric, ...] to temporarily replace DB values.
     */
    public function evaluateAll(int $year, int $month, array $overrides = []): array
    {
        // Load metadata
        $items = FinancialLineItem::orderBy('display_order')->get()
            ->keyBy('code'); // code => model

        // Load editable values for the month (line_item_id => float)
        $valuesByLineId = $this->loadEditableValues($year, $month);

        // Map overrides from code->value into line_item_id->value
        foreach ($overrides as $code => $val) {
            if (isset($items[$code])) {
                $valuesByLineId[$items[$code]->id] = (float)$val;
            }
        }

        // Build formula map: code => formula
        $formulaMaps = FinancialFormulaMap::all();
        $formulas = [];
        foreach ($formulaMaps as $fm) {
            $line = FinancialLineItem::find($fm->financial_line_item_id);
            if (! $line) continue;
            $formulas[$line->code] = $fm->formula;
        }

        // Compute derived values
        $derived = [];

        // Build dependencies and topo order
        $deps = $this->buildDependencyGraph($formulas);
        $order = $this->topoSort($deps);

        foreach ($order as $code) {
            $formula = $formulas[$code] ?? null;
            if ($formula === null) continue;

            $value = $this->evaluateFormulaForCode($formula, $code, $valuesByLineId, $derived, $items, $year, $month);
            $derived[$code] = $value;
        }

        // Build final result map combining editable and derived (returns numeric in 'value')
        $result = [];
        foreach ($items as $code => $item) {
            $lineId = $item->id;
            $isEditable = (bool) $item->is_editable;
            $editableValue = $valuesByLineId[$lineId] ?? 0.0;
            $derivedValue = $derived[$code] ?? null;

            $finalValue = $isEditable ? $editableValue : ($derivedValue ?? 0.0);

            $result[$code] = [
                'line_item_id' => $lineId,
                'code' => $code,
                'pdf_column' => $item->pdf_column,
                'editable' => $isEditable,
                'value' => (float) $finalValue,
            ];
        }

        return $result;
    }

    protected function loadEditableValues(int $year, int $month): array
    {
        $rows = FinancialValue::where('year', $year)
            ->where('month', $month)
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->financial_line_item_id] = (float) $r->value;
        }

        return $map;
    }

    /**
     * Build dependency graph keyed by formula code:
     * ['a' => ['b','c'], ...] meaning a depends on b,c
     */
    protected function buildDependencyGraph(array $formulas): array
    {
        $graph = [];
        foreach ($formulas as $code => $formula) {
            $tokens = $this->extractTokensFromFormula($formula);
            // remove pseudo-words that are function names like SUM
            $tokens = array_filter($tokens, fn($t) => strtoupper($t) !== 'SUM');
            // only keep unique and non-numeric tokens
            $tokens = array_values(array_unique(array_filter($tokens, fn($t) => preg_match('/^[a-z_][a-z0-9_]*$/i', $t))));
            $graph[$code] = $tokens;
        }
        return $graph;
    }

    /**
     * Topologically sort dependency graph.
     * Throws RuntimeException if a cycle is detected.
     */
    protected function topoSort(array $graph): array
    {
        $visited = []; // 0=unseen, 1=visiting, 2=done
        $order = [];

        $visit = function($node) use (&$visit, &$visited, &$order, $graph) {
            if (!array_key_exists($node, $visited)) $visited[$node] = 0;
            if ($visited[$node] === 1) {
                // cycle
                throw new RuntimeException("Circular reference detected involving '{$node}'");
            }
            if ($visited[$node] === 2) return;
            $visited[$node] = 1;
            foreach ($graph[$node] ?? [] as $dep) {
                // only visit if dep is itself a formula (skip pure editables)
                if (array_key_exists($dep, $graph)) {
                    $visit($dep);
                }
            }
            $visited[$node] = 2;
            $order[] = $node;
        };

        foreach (array_keys($graph) as $n) {
            if (!isset($visited[$n]) || $visited[$n] === 0) $visit($n);
        }

        return $order;
    }

    /**
     * Evaluate a single formula for a code using current editable and derived maps.
     */
    protected function evaluateFormulaForCode(string $formula, string $targetCode, array $editableValuesByLineId, array $derivedByCode, Collection $items, int $year, int $month): float
    {
        // Replace SUM(someList) -> (a+b+c) style
        $formula = $this->expandSumFunctions($formula);

        // Replace tokens with numeric values
        $replaced = preg_replace_callback('/[A-Za-z_][A-Za-z0-9_]*/', function($m) use ($editableValuesByLineId, $derivedByCode, $items, $year, $month, $targetCode) {
            $tok = $m[0];

            // SUM handled earlier; function names excluded
            // First, if token equals target code (self-ref) return 0 to avoid trivial self ref
            if ($tok === $targetCode) return '0';

            // If token corresponds to a line item in items -> get its numeric value:
            $item = $items->get($tok);
            if ($item) {
                $id = $item->id;
                // editable?
                if ($item->is_editable) {
                    return (string) ($editableValuesByLineId[$id] ?? 0);
                } else {
                    // derived: maybe already computed in $derivedByCode
                    if (array_key_exists($tok, $derivedByCode)) {
                        return (string) $derivedByCode[$tok];
                    }
                    // derived not yet computed -> 0 fallback (it may be computed later by topo order)
                    return '0';
                }
            }

            // If token is not a line item code, but numeric-like, return as is (e.g., '12')
            if (is_numeric($tok)) return $tok;

            // Unknown token — treat as zero
            return '0';
        }, $formula);

        // Validate expression contains ONLY allowed characters now
        if (!preg_match('/^[0-9\.\+\-\*\/\(\)\s,]*$/', $replaced)) {
            // If commas remain (from malformed SUM) remove them
            $replaced = str_replace(',', '+', $replaced);
            // re-validate
            if (!preg_match('/^[0-9\.\+\-\*\/\(\)\s]*$/', $replaced)) {
                throw new RuntimeException("Unsafe or invalid formula after token replacement for '{$targetCode}': {$replaced}");
            }
        }

        // Evaluate safely
        // Wrap in (float) to ensure numeric
        try {
            // Evaluate using PHP's eval — restricted by prior validation
            $val = 0.0;
            // handle empty or whitespace-only
            if (trim($replaced) === '') return 0.0;
            $val = eval('return (' . $replaced . ');');
            return is_numeric($val) ? (float) $val : 0.0;
        } catch (\Throwable $e) {
            throw new RuntimeException("Error evaluating formula for {$targetCode}: " . $e->getMessage());
        }
    }

    /**
     * Extract tokens (identifiers) from a formula string.
     */
    protected function extractTokensFromFormula(string $formula): array
    {
        preg_match_all('/[A-Za-z_][A-Za-z0-9_]*/', $formula, $m);
        return $m[0] ?? [];
    }

    /**
     * Expand SUM(...) occurrences to (a+b+...) so the evaluator can handle them simply.
     * Supports comma or plus separated lists inside SUM.
     */
    protected function expandSumFunctions(string $formula): string
    {
        // handle nested SUMs by looping until no more SUM(
        while (preg_match('/SUM\s*\(([^()]*)\)/i', $formula, $m)) {
            $inner = $m[1];
            // replace commas with +, and multiple + normalized
            $expanded = preg_replace('/\s*,\s*/', '+', $inner);
            $expanded = preg_replace('/\s*\+\s*/', '+', $expanded);
            $replacement = '(' . $expanded . ')';
            $formula = preg_replace('/SUM\s*\(' . preg_quote($m[1], '/') . '\)/i', $replacement, $formula, 1);
        }
        return $formula;
    }
}
