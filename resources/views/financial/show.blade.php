@extends('layouts.app')

@section('content')
<div class="container mb-5">

    {{-- PAGE HEADER --}}
    <h2 class="fw-bold mb-4 text-center text-uppercase">
        Statement of Financial Position – {{ $monthName }} {{ $year }}
    </h2>

    {{-- Expose items to JS --}}
    <script>
        window.FINANCIAL_ITEMS = @json($lineItems);
    </script>

    {{-- Table Wrapper --}}
    <div class="table-responsive shadow-sm border rounded bg-white p-3">

        <table class="table table-borderless align-middle mb-0">

            @foreach($lineItems as $item)

                {{-- SECTION HEADERS --}}
                @if($item->type === 'section')
                    <tr class="bg-secondary bg-opacity-10">
                        <td class="fw-bold fs-5 py-2" colspan="4">
                            {{ $item->label }}
                        </td>
                    </tr>
                    @continue
                @endif

                {{-- SUBTITLES --}}
                @if($item->type === 'subtitle')
                    <tr>
                        <td class="fw-semibold ps-4 text-muted">
                            {{ $item->label }}
                        </td>
                        <td style="width:150px;"></td>
                        <td style="width:150px;"></td>
                        <td style="width:170px;"></td>
                    </tr>
                    @continue
                @endif

                {{-- SPACER ROW --}}
                @if($item->type === 'spacer')
                    <tr><td colspan="4" style="height: 10px;"></td></tr>
                    @continue
                @endif

                {{-- NORMAL + DERIVED ITEMS --}}
                @php
                    $labelClass = $item->is_derived ? 'fw-bold text-dark' : '';
                    $indent = "ps-" . ($item->indent ?? 1);
                @endphp

                <tr class="item-row">

                    {{-- COLUMN 1 — LABEL --}}
                    <td class="{{ $labelClass }} {{ $indent }}">
                        {{ $item->label }}
                    </td>

                    {{-- COLUMN 2 --}}
                    <td class="text-end" style="width:150px;">
                        @if($item->col2_editable)
                            <input type="number" step="0.01"
                                   class="form-control form-control-sm text-end editable-field"
                                   name="col2[{{ $item->code }}]"
                                   value="{{ $item->value_col2 }}">
                        @else
                            <span data-code="{{ $item->code }}" data-col="2">
                                {{ number_format($item->value_col2, 2) }}
                            </span>
                        @endif
                    </td>

                    {{-- COLUMN 3 --}}
                    <td class="text-end" style="width:150px;">
                        @if($item->col3_editable)
                            <input type="number" step="0.01"
                                   class="form-control form-control-sm text-end editable-field"
                                   name="col3[{{ $item->code }}]"
                                   value="{{ $item->value_col3 }}">
                        @else
                            <span data-code="{{ $item->code }}" data-col="3"
                                class="{{ $item->is_derived ? 'fw-bold text-dark' : '' }}">
                                {{ number_format($item->value_col3, 2) }}
                            </span>
                        @endif
                    </td>

                    {{-- COLUMN 4 --}}
                    <td class="text-end" style="width:170px;">
                        @if($item->is_editable)
                            <input type="number" step="0.01"
                                   class="form-control form-control-sm text-end editable-field"
                                   name="col4[{{ $item->code }}]"
                                   value="{{ $item->value_col4 }}">
                        @else
                            <span data-code="{{ $item->code }}" data-col="4"
                                  class="fw-bold text-primary">
                                {{ number_format($item->value_col4, 2) }}
                            </span>
                        @endif
                    </td>

                </tr>

                {{-- Divider under grand totals --}}
                @if(in_array($item->code, ['total_assets', 'total_liabilities_and_equity']))
                    <tr>
                        <td colspan="4"><hr class="mt-2 mb-2"></td>
                    </tr>
                @endif

            @endforeach

        </table>

        {{-- Accounting Equation Notice --}}
        <div id="ae-alert" class="mt-3"></div>

    </div>

</div>

{{-- Additional Styling --}}
<style>
    .editable-field {
        background: #fff7d6 !important;
        border: 1px solid #e3d9a5;
        box-shadow: none !important;
        font-weight: 500;
    }

    /* Remove number spinner */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }

    .item-row:hover td {
        background: #f8f9fa;
    }
</style>

@endsection



@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

    const items = window.FINANCIAL_ITEMS;
    const inputSelectors = "input[type='number']";

    // Attach listeners
    document.querySelectorAll(inputSelectors).forEach(input => {
        input.addEventListener("input", recalcAll);
    });

    function recalcAll() {
        const values = collectEditableValues();
        const derived = calculateDerived(values);
        applyToUI(values, derived);
        checkAccountingEquation(derived);
    }

    // Collect editable values
    function collectEditableValues() {
        let map = {};

        items.forEach(item => {
            map[item.code] = {
                col2: item.col2_editable ? getInput(`col2[${item.code}]`) : (item.value_col2 ?? 0),
                col3: item.col3_editable ? getInput(`col3[${item.code}]`) : (item.value_col3 ?? 0),
                col4: item.is_editable    ? getInput(`col4[${item.code}]`) : (item.value_col4 ?? 0)
            };
        });

        return map;
    }

    function getInput(name) {
        const el = document.querySelector(`[name="${name}"]`);
        return el ? parseFloat(el.value || 0) : 0;
    }

    // Derived calculations
    function calculateDerived(values) {
        let derived = {};
        let safeguard = 0;

        const get = (code, col) => {
            if (values[code]) return values[code][col] || 0;
            if (derived[code]) return derived[code][col] || 0;
            return 0;
        };

        while (safeguard++ < 50) {
            let allGood = true;

            items.forEach(item => {
                if (!item.formula) return;

                const targetCol = item.formula_column || 'col4';
                const expr = item.formula;

                try {
                    const resolved = expr.replace(/[A-Za-z0-9_]+/g, tok => {
                        if (tok === item.code) return 0;
                        return get(tok, targetCol);
                    });

                    const result = Function(`return (${resolved})`)();

                    derived[item.code] = { col2:0, col3:0, col4:0 };
                    derived[item.code][targetCol] = result;
                }
                catch {
                    allGood = false;
                }
            });

            if (allGood) break;
        }

        return derived;
    }

    // Update UI
    function applyToUI(values, derived) {

        items.forEach(item => {
            const final = derived[item.code] || values[item.code];

            // Column 2
            if (!item.col2_editable) updateSpan(item.code, 2, final.col2);

            // Column 3
            if (!item.col3_editable) updateSpan(item.code, 3, final.col3);

            // Column 4
            if (!item.is_editable) updateSpan(item.code, 4, final.col4);
        });
    }

    function updateSpan(code, col, value) {
        const span = document.querySelector(`[data-code="${code}"][data-col="${col}"]`);
        if (span) span.textContent = number(value);
    }

    function number(n) {
        return parseFloat(n || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Accounting Equation Check
    function checkAccountingEquation(derived) {
        const totalAssets = derived['total_assets']?.col4 ?? 0;
        const totalLE     = derived['total_liabilities_and_equity']?.col4 ?? 0;

        const alert = document.getElementById("ae-alert");

        const diff = totalAssets - totalLE;

        if (Math.abs(diff) < 0.50) {
            alert.className = "alert alert-success";
            alert.innerHTML = `<strong>Balanced:</strong> Assets = Liabilities + Equity`;
        } else {
            alert.className = "alert alert-danger";
            alert.innerHTML = `<strong>Unbalanced:</strong> Difference = ${number(diff)}`;
        }
    }

});
</script>
@endsection
