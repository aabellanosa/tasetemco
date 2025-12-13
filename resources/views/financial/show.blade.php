@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2 class="mb-4">Financial Statement – {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</h2>

    @if (session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            ❌ {{ session('error') }}
        </div>
    @endif


    {{-- Accounting Equation Warning --}}
    <div id="equationAlert" class="alert alert-danger d-none">
        ⚠ <strong>Warning:</strong> Assets ≠ Liabilities + Equity
        <div id="equationDiff" class="small mt-2"></div>
    </div>

    <div class="mb-3">
        <small class="text-muted">Edit any numeric field — derived values update instantly.</small>
    </div>

    <form id="fsForm" 
        method="POST" 
        action="{{ route('financial.save')}}">
        @csrf

        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="month" value="{{ $month }}">

        
        <div class="table-responsive table-container">
            <table class="table table-bordered align-middle" id="financialTable">
                <thead class="table-light sticky-header">
                    <tr>
                        <th style="width: 40%">Description</th>
                        <th style="width: 20%" class="text-end">Col 2</th>
                        <th style="width: 20%" class="text-end">Col 3</th>
                        <th style="width: 20%" class="text-end">Col 4</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lineItems as $item)
                        <tr data-code="{{ $item['code'] }}">
                            <td>
                                <div style="margin-left: {{ ($item['indent'] ?? 0) * 20 }}px;">
                                    @if(!$item['editable'])
                                        <strong>{{ $item['title'] }}</strong>
                                    @else
                                        {{ $item['title'] }}
                                    @endif
                                </div>
                            </td>
    
                            {{-- Column 2 --}}
                            <td class="text-end">
                                @if($item['pdf_column'] == 2)
                                    @if($item['editable'])
                                        <input 
                                            type="text" 
                                            inputmode="decimal" class="form-control text-end editable-input"
                                            name="values[{{ $item['code'] }}]"
                                            data-code="{{ $item['code'] }}" data-col="2"
                                            value="{{ number_format($item['col2'] ?? 0, 2, '.', '') }}">
                                    @else
                                        <strong class="derived-value" data-code="{{ $item['code'] }}" data-col="2">
                                            {{ number_format($item['col2'] ?? 0, 2) }}
                                        </strong>
                                    @endif
                                @else
                                    @if(isset($item['col2']) && $item['col2'] !== null)
                                        <strong class="derived-value" data-code="{{ $item['code'] }}" data-col="2">
                                            {{ number_format($item['col2'], 2) }}
                                        </strong>
                                    @endif
                                @endif
                            </td>
    
                            {{-- Column 3 --}}
                            <td class="text-end">
                                @if($item['pdf_column'] == 3)
                                    @if($item['editable'])
                                        <input 
                                            type="text" 
                                            inputmode="decimal" class="form-control text-end editable-input"
                                            name="values[{{ $item['code'] }}]"
                                            data-code="{{ $item['code'] }}" data-col="3"
                                            value="{{ number_format($item['col3'] ?? 0, 2, '.', '') }}">
                                    @else
                                        <strong class="derived-value" data-code="{{ $item['code'] }}" data-col="3">
                                            {{ number_format($item['col3'] ?? 0, 2) }}
                                        </strong>
                                    @endif
                                @else
                                    @if(isset($item['col3']) && $item['col3'] !== null)
                                        <strong class="derived-value" data-code="{{ $item['code'] }}" data-col="3">
                                            {{ number_format($item['col3'], 2) }}
                                        </strong>
                                    @endif
                                @endif
                            </td>
    
                            {{-- Column 4 --}}
                            <td class="text-end">
                                @if($item['pdf_column'] == 4)
                                    @if($item['editable'])
                                        <input 
                                            type="text" 
                                            inputmode="decimal" 
                                            class="form-control text-end editable-input"
                                            name="values[{{ $item['code'] }}]"
                                            data-code="{{ $item['code'] }}" data-col="4"
                                            value="{{ number_format($item['col4'] ?? 0, 2, '.', '') }}">
                                    @else
                                        <strong class="derived-value" data-code="{{ $item['code'] }}" data-col="4">
                                            {{ number_format($item['col4'] ?? 0, 2) }}
                                        </strong>
                                    @endif
                                @else
                                    @if(isset($item['col4']) && $item['col4'] !== null)
                                        <strong class="derived-value" data-code="{{ $item['code'] }}" data-col="4">
                                            {{ number_format($item['col4'], 2) }}
                                        </strong>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">
                Save Financial Statement
            </button>
        </div>
        

    </form>

    


</div>
@endsection


@section('scripts')
<script>
/*
  Live client-side evaluator (Option A)
  - Uses server-supplied formulas JSON (single source of truth)
  - Supports SUM(...), + - * / and parentheses
  - Applies the same special multi-column rules as server
  - Recalculates on any editable input change
  - Shows accounting equation (assets == liabilities + equity) alert
*/

(() => {
    let isBalanced = true;

    // Server-provided formulas (array of {code, formula})
    const FORMULAS = @json($formulas);

    // Build formula map for quick access: code -> formulaString
    const formulaMap = {};
    FORMULAS.forEach(f => formulaMap[f.code] = f.formula);

    // Helper: parse localized input to number
    const parseNum = (s) => {
        if (s === null || s === undefined || s === '') return 0;
        // remove commas and spaces
        return Number(String(s).replace(/,/g, '').trim()) || 0;
    };

    // Helper: format number to 2 decimals with commas
    const fmt = (n) => {
        return Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    // Collect initial editable values from inputs (code => numeric)
    const collectEditableValues = () => {
        const out = {};
        document.querySelectorAll('.editable-input').forEach(inp => {
            out[inp.dataset.code] = parseNum(inp.value);
        });
        return out;
    };

    // Expand SUM(foo,bar) -> (foo+bar)
    function expandSum(expr) {
        // repeat until no more SUM(
        let s = expr;
        const re = /SUM\s*\(\s*([^\(\)]*?)\s*\)/i;
        while (re.test(s)) {
            s = s.replace(re, function(_, inner) {
                // replace commas with +, normalize spaces
                const cleaned = inner.replace(/\s*,\s*/g, '+').replace(/\s*\+\s*/g, '+');
                return '(' + cleaned + ')';
            });
        }
        return s;
    }

    // Safe token replacement: replace code tokens with numeric from values
    function replaceTokens(expr, values) {
        // Replace tokens like word_word123 with values[word]
        return expr.replace(/[A-Za-z_][A-Za-z0-9_]*/g, function(tok) {
            // tokens that are function names (SUM) already expanded earlier; treat as 0 if unknown
            if (Object.prototype.hasOwnProperty.call(values, tok)) {
                return '(' + Number(values[tok]) + ')';
            }
            // if token is numeric already, keep
            if (!isNaN(Number(tok))) return tok;
            // unknown token => 0
            return '(0)';
        });
    }

    // Safe evaluate arithmetic expression (only numbers and operators)
    function safeEval(expr) {
        // allow digits, dot, parentheses and + - * / whitespace
        if (!/^[0-9\.\+\-\*\/\(\)\s]+$/.test(expr)) {
            // if contains unexpected chars, return 0
            return 0;
        }
        try {
            return Function('"use strict"; return (' + expr + ')')();
        } catch (e) {
            console.error('Eval error', e, expr);
            return 0;
        }
    }

    // Evaluate all formulas given a base values map (editable overrides).
    // Returns derivedMap: code => numeric value
    function evaluateFormulas(baseValues) {
        // derived computed map
        const derived = {};

        // We'll iterate formulas repeatedly until no change (or max iterations)
        const maxIter = 30;
        let iter = 0;
        let changed = true;

        // Preprocess formula expressions (expand SUM once)
        const pre = {};
        for (const code in formulaMap) {
            pre[code] = expandSum(formulaMap[code]);
        }

        while (changed && iter < maxIter) {
            changed = false;
            iter++;

            for (const code in pre) {
                const expr = pre[code];

                // Build token map: tokens get values from baseValues OR derived if available
                const tokenValues = Object.assign({}, baseValues, derived);

                // replace tokens
                const replaced = replaceTokens(expr, tokenValues);

                // validate allowed chars
                const numeric = safeEval(replaced) || 0;

                if (!Object.is(derived[code], numeric)) {
                    derived[code] = numeric;
                    changed = true;
                }
            }
        }

        // final pass to ensure numeric
        for (const k in derived) derived[k] = Number(derived[k] || 0);

        return derived;
    }

    // Apply special multi-column rules (Option A) and produce final col map for each code
    function applySpecialMultiColumnRules(values) {
        // values: code => numeric (these are the "final" value for each logical code)
        // We need to compute siblings for specific codes and return per-code col2/3/4 numeric values (or null)

        const cols = {}; // code => {col2, col3, col4}

        // helper to safe get numeric
        const g = (c) => Number(values[c] || 0);

        // fill base: each code defaults to its server pdf_column slot; for live we will map these later in DOM update
        for (const code in values) {
            cols[code] = { col2: null, col3: null, col4: null };
        }

        // Place primary values based on lineItems pdf_column info (we read from DOM rows to determine pdf_column)
        document.querySelectorAll('tr[data-code]').forEach(row => {
            const code = row.dataset.code;
            const pdfColAttr = row.querySelector('.editable-input, .derived-value')?.dataset?.col;
            // If dataset col present (editable/derived element), prefer that, otherwise try server-supplied initial (we can't rely on that here),
            // but easiest is to check which column contains an element with data-col attr.
            // We'll set primary into whichever element exists with data-col.
            const primaryEl = row.querySelector('[data-col]');
            if (primaryEl) {
                const col = parseInt(primaryEl.dataset.col, 10);
                if (!isNaN(col)) {
                    cols[code]['col' + col] = g(code);
                }
            }
        });

        // SPECIAL 1: ONE COOP -> col4 = sum(lbp_ca + lbp_savings + dbp_savings + one_coop)
        cols['one_coop'] = cols['one_coop'] || {col2:null,col3:null,col4:null};
        cols['one_coop'].col4 = g('lbp_ca') + g('lbp_savings') + g('dbp_savings') + g('one_coop');

        // SPECIAL 2: ALLOWANCE FOR PROBABLE LOSSES (allowance_probable)
        cols['allowance_probable'] = cols['allowance_probable'] || {col2:null,col3:null,col4:null};
        cols['allowance_probable'].col3 = g('past_due') - g('allowance_probable');
        cols['allowance_probable'].col4 = g('regular_loans') + g('associates') + g('micro_project') + cols['allowance_probable'].col3;

        // SPECIAL 3: investment_ccb_cbss -> col3 = sum(investment_pftech + investment_climbs + investment_ccb_cbss)
        cols['investment_ccb_cbss'] = cols['investment_ccb_cbss'] || {col2:null,col3:null,col4:null};
        cols['investment_ccb_cbss'].col3 = g('investment_pftech') + g('investment_climbs') + g('investment_ccb_cbss');

        // SPECIAL 4: loans_ccb_cbss -> col3 = sum(loans_lbp + loans_lgu + loans_ccb_cbss)
        cols['loans_ccb_cbss'] = cols['loans_ccb_cbss'] || {col2:null,col3:null,col4:null};
        cols['loans_ccb_cbss'].col3 = g('loans_lbp') + g('loans_lgu') + g('loans_ccb_cbss');

        // SPECIAL 5: optional_fund -> col3 = sum(reserve_fund + education_training + community_dev + optional_fund)
        cols['optional_fund'] = cols['optional_fund'] || {col2:null,col3:null,col4:null};
        cols['optional_fund'].col3 = g('reserve_fund') + g('education_training') + g('community_dev') + g('optional_fund');

        // For safety, ensure numeric and return.
        for (const c in cols) {
            if (cols[c].col2 !== null) cols[c].col2 = Number(cols[c].col2 || 0);
            if (cols[c].col3 !== null) cols[c].col3 = Number(cols[c].col3 || 0);
            if (cols[c].col4 !== null) cols[c].col4 = Number(cols[c].col4 || 0);
        }

        return cols; // map code => {col2,col3,col4}
    }

    // Update DOM derived fields and inputs (do not change which inputs are editable)
    function applyToDom(derivedValues) {
        // 1) Update simple derived-value spans
        document.querySelectorAll('.derived-value').forEach(el => {
            const code = el.dataset.code;
            const newVal = derivedValues[code] || 0;
            el.textContent = fmt(newVal);
        });

        // 2) Update any input values if server-derived normalization needed (we'll not overwrite user typing)
        // Skip to avoid interfering with typing.

        // 3) Apply multi-column special computed siblings:
        const cols = applySpecialMultiColumnRules(derivedValues);

        // For each row, update the column slot if present
        document.querySelectorAll('tr[data-code]').forEach(row => {
            const code = row.dataset.code;
            const col2El = row.querySelector('[data-col="2"]');
            const col3El = row.querySelector('[data-col="3"]');
            const col4El = row.querySelector('[data-col="4"]');

            // If a slot exists AND it's a derived display element (strong.derived-value), update it
            if (cols[code]) {
                if (col2El && col2El.classList.contains('derived-value')) col2El.textContent = fmt(cols[code].col2 || 0);
                if (col3El && col3El.classList.contains('derived-value')) col3El.textContent = fmt(cols[code].col3 || 0);
                if (col4El && col4El.classList.contains('derived-value')) col4El.textContent = fmt(cols[code].col4 || 0);
            }

            // Also, for rows where pdf_column is not the slot but the derived sibling was placed previously (like optional_fund), update the sibling element if it exists
            if (cols[code]) {
                // find any derived-value element in the row without data-col match and update
                row.querySelectorAll('.derived-value').forEach(el => {
                    const targetCol = el.dataset.col ? parseInt(el.dataset.col,10) : null;
                    if (!targetCol) return;
                    const valname = 'col' + targetCol;
                    if (cols[code][valname] !== undefined && cols[code][valname] !== null) {
                        el.textContent = fmt(cols[code][valname]);
                    }
                });
            }
        });
    }

    // Accounting equation check
    function checkAccountingEquation(derivedValues) {
        const assets = Number(derivedValues['total_assets'] || 0);
        const liabilities = Number(derivedValues['total_liabilities'] || 0);
        const equity = Number(derivedValues['total_equity'] || 0);

        const diff = assets - (liabilities + equity);
        const alert = document.getElementById('equationAlert');
        const diffEl = document.getElementById('equationDiff');

        if (Math.abs(diff) < 0.005) {
            alert.classList.add('d-none');
            if (diffEl) diffEl.textContent = '';
            isBalanced = true;
            return true;
        } else {
            alert.classList.remove('d-none');
            if (diffEl) diffEl.textContent = 'Difference: ' + fmt(diff) + ' (Assets - (Liabilities + Equity))';
            isBalanced = false;
            return false;
        }
    }

    // Main recalculation driver
    function recalcDriver() {
        const base = collectEditableValues();
        const derived = evaluateFormulas(base);

        // Merge base editable values into derived map so derivedMap[code] returns final numeric for every code
        const merged = Object.assign({}, derived, base);

        // Apply to DOM (derived spans and multi-column siblings)
        applyToDom(merged);

        // Check accounting equation
        checkAccountingEquation(merged);
    }

    // Setup listeners (debounced)
    let debounceTimer = null;
    function scheduleRecalc() {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(recalcDriver, 250);
    }

    document.querySelectorAll('.editable-input').forEach(inp => {
        inp.addEventListener('input', scheduleRecalc);
    });

    // initial run (populate derived fields after page load)
    window.addEventListener('load', () => {
        // initial recalc
        recalcDriver();
    });

    const fsForm = document.getElementById('fsForm');

    fsForm.addEventListener('submit', function(e) {
        if (!isBalanced) {
            e.preventDefault();
            alert('Financial Statement is NOT balanced.\nPlease correct before saving.');
        }
    });

})();
</script>
@endsection
