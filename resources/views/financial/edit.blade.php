@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2 class="fw-bold mb-4">
        Financial Position – {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Accounting Equation Alert Placeholder --}}
    <div id="accountingAlert"></div>

    <form method="POST" action="{{ route('financial.update', [$year, $month]) }}">
        @csrf

        <div class="card shadow-sm">
            <div class="card-body">

                @foreach ($items as $item)

                    @if ($item->is_section_header)
                        <x-section-header :label="$item->name"/>
                    @elseif (!$item->is_editable)
                        <x-total-row 
                            :label="$item->name"
                            :code="$item->code"
                            :value="$values[$item->code]"
                            :formula="$formulas[$item->id]->formula ?? null"
                        />
                    @else
                        <x-line-item-row 
                            :label="$item->name"
                            :code="$item->code"
                            :value="$values[$item->code]"
                        />
                    @endif

                @endforeach

            </div>
        </div>

        <div class="mt-3 text-end">
            <button class="btn btn-primary btn-lg">Save</button>
        </div>

    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    function recalc() {

        let totals = {};

        // 1. Gather raw input values
        document.querySelectorAll(".value-input").forEach(el => {
            totals[el.dataset.code] = parseFloat(el.value || 0);
        });

        // 2. Compute totals using formulas embedded in DOM
        document.querySelectorAll("[data-formula]").forEach(el => {
            let formula = el.dataset.formula;

            let expr = formula.replace(/[a-z_]+/gi, m => totals[m] ?? 0);

            let val = Function("return " + expr)();

            totals[el.dataset.code] = val;

            el.textContent = val.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        });

        // ACCOUNTING EQUATION CHECK
        let assets = totals['total_assets'] ?? 0;
        let liabilities_equity = totals['total_liabilities_and_equity'] ?? 0;

        let alertDiv = document.getElementById('accountingAlert');
        alertDiv.innerHTML = '';

        if (Math.abs(assets - liabilities_equity) < 0.01) {
            alertDiv.innerHTML = `
                <div class="alert alert-success">
                    ✔ Balanced: Total Assets matches Total Liabilities + Equity
                </div>`;
        } else {
            alertDiv.innerHTML = `
                <div class="alert alert-danger">
                    ⚠ Imbalance Detected: Assets (${assets}) ≠ Liabilities + Equity (${liabilities_equity})
                </div>`;
        }
    }

    // Bind recalculation
    document.querySelectorAll(".value-input").forEach(el => {
        el.addEventListener("input", recalc);
    });

    recalc();
});
</script>

@endsection
