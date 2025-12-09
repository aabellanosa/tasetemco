<?php

namespace App\Http\Controllers;

use App\Models\FinancialFormulaMap;
use App\Models\FinancialLineItem;
use App\Models\FinancialValue;
use App\Services\FinancialRollOverService;
use App\Services\FinancialFormulaEvaluator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
 
    public function show($year, $month)
    {
        $year = (int)$year;
        $month = (int)$month;

        // evaluator (server-side) to produce initial/resolved numbers (used for initial rendering)
        $evaluator = new \App\Services\FinancialFormulaEvaluator();
        $resolved = $evaluator->evaluateAll($year, $month);

        // helper to get numeric final value for any code (falls back to DB if missing)
        $getNumeric = function(string $code) use ($resolved, $year, $month) : float {
            if (isset($resolved[$code]) && is_array($resolved[$code]) && isset($resolved[$code]['value'])) {
                return (float)$resolved[$code]['value'];
            }
            $item = \App\Models\FinancialLineItem::where('code', $code)->first();
            if ($item) {
                $dbVal = \App\Models\FinancialValue::where('financial_line_item_id', $item->id)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->value('value');
                return is_null($dbVal) ? 0.0 : (float)$dbVal;
            }
            return 0.0;
        };

        // load metadata in order
        $items = \App\Models\FinancialLineItem::orderBy('display_order')->get();

        // Build lineItems with explicit col2/col3/col4 slots (as we implemented earlier)
        $lineItems = $items->map(function ($item) use ($year, $month, $resolved, $getNumeric) {

            $col2 = null; $col3 = null; $col4 = null;

            // editable
            $editableValue = null;
            if ((bool)$item->is_editable) {
                $dbVal = \App\Models\FinancialValue::where('financial_line_item_id', $item->id)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->value('value');

                $editableValue = is_null($dbVal) ? 0.0 : (float)$dbVal;
                $targetCol = (int)$item->pdf_column;
                if ($targetCol === 2) $col2 = $editableValue;
                if ($targetCol === 3) $col3 = $editableValue;
                if ($targetCol === 4) $col4 = $editableValue;
            }

            // derived primary value
            if (!(bool)$item->is_editable) {
                if (isset($resolved[$item->code]) && is_array($resolved[$item->code])) {
                    $derivedValue = (float)($resolved[$item->code]['value'] ?? 0.0);
                    $targetCol = (int)$item->pdf_column;
                    if ($targetCol === 2) $col2 = $derivedValue;
                    if ($targetCol === 3) $col3 = $derivedValue;
                    if ($targetCol === 4) $col4 = $derivedValue;
                }
            }

            // Special multi-column rules (same as locked rules)
            if ($item->code === 'one_coop') {
                $sum = 0.0;
                $sum += $getNumeric('lbp_ca');
                $sum += $getNumeric('lbp_savings');
                $sum += $getNumeric('dbp_savings');
                $sum += $getNumeric('one_coop');
                $col4 = (float)$sum;
            }

            if ($item->code === 'allowance_probable') {
                $allow = $editableValue ?? $getNumeric('allowance_probable');
                $pastDue = $getNumeric('past_due');
                $col3 = (float)($pastDue - $allow);

                $regular = $getNumeric('regular_loans');
                $assoc = $getNumeric('associates');
                $micro = $getNumeric('micro_project');
                $col4 = (float)($regular + $assoc + $micro + $col3);
            }

            if ($item->code === 'investment_ccb_cbss') {
                $col3 = (float)($getNumeric('investment_pftech') + $getNumeric('investment_climbs') + $getNumeric('investment_ccb_cbss'));
            }

            if ($item->code === 'loans_ccb_cbss') {
                $col3 = (float)($getNumeric('loans_lbp') + $getNumeric('loans_lgu') + $getNumeric('loans_ccb_cbss'));
            }

            if ($item->code === 'optional_fund') {
                $col3 = (float)($getNumeric('reserve_fund') + $getNumeric('education_training') + $getNumeric('community_dev') + $getNumeric('optional_fund'));
            }

            return [
                'id' => $item->id,
                'code' => $item->code,
                'title' => $item->label ?? $item->title ?? $item->code,
                'indent' => $item->indent_level ?? $item->indent ?? 0,
                'pdf_column' => (int)$item->pdf_column,
                'editable' => (bool)$item->is_editable,
                'col2' => is_null($col2) ? null : (float)$col2,
                'col3' => is_null($col3) ? null : (float)$col3,
                'col4' => is_null($col4) ? null : (float)$col4,
            ];
        })->values()->toArray();

        // Build formulas payload for JS (pull formula string keyed by code)
        $formulaRows = \App\Models\FinancialFormulaMap::with('lineItem')->get();
        $formulas = [];
        foreach ($formulaRows as $r) {
            $li = $r->lineItem;
            if ($li) {
                $formulas[] = [
                    'code' => $li->code,
                    'formula' => $r->formula,
                ];
            }
        }

        return view('financial.show', [
            'year' => $year,
            'month' => $month,
            'lineItems' => $lineItems,
            'formulas' => $formulas,
        ]);
    }


        /**
     * POST /financial/recalc
     * Accepts JSON: { year, month, values: { code: number, ... } }
     * Returns { values: { code: number }, equation_ok: bool }
     */
    public function recalc(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'values' => 'required|array',
        ]);

        $year = (int)$data['year'];
        $month = (int)$data['month'];
        $overrides = $data['values'];

        $evaluator = new FinancialFormulaEvaluator();
        $map = $evaluator->evaluateAll($year, $month, $overrides);

        // Build code => numeric map to return
        $values = [];
        foreach ($map as $code => $row) {
            $values[$code] = isset($row['value']) ? (float)$row['value'] : 0.0;
        }

        // quick accounting equation check: assets == liabilities + equity (tolerance = 0.01)
        $assets = ($values['total_assets'] ?? 0.0);
        $liabilities = ($values['total_liabilities'] ?? 0.0);
        $equity = ($values['total_equity'] ?? 0.0);
        $equation_ok = (abs($assets - ($liabilities + $equity)) < 0.01);

        return response()->json([
            'values' => $values,
            'equation_ok' => $equation_ok,
        ]);
    }

    /**
     * POST /financial/save
     * Persists editable values: JSON { year, month, values: { code: number, ... } }
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'values' => 'required|array',
        ]);

        $year = (int)$data['year'];
        $month = (int)$data['month'];
        $values = $data['values'];

        DB::beginTransaction();
        try {
            foreach ($values as $code => $val) {
                $code = (string)$code;
                $numeric = is_numeric($val) ? (float)$val : 0.0;

                $line = FinancialLineItem::where('code', $code)->first();
                if (! $line) {
                    // skip unknown codes
                    continue;
                }
                if (! $line->is_editable) {
                    // skip non-editable
                    continue;
                }

                // upsert financial_values (line_item_id,year,month)
                FinancialValue::updateOrCreate(
                    [
                        'financial_line_item_id' => $line->id,
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'value' => $numeric,
                    ]
                );
            }
            DB::commit();
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /financial/rollover
     * Body: { from_year, from_month, to_year, to_month, copy_codes?: [code,...] (optional) }
     * Simple: copies editable values from source to destination.
     */
    public function rollover(Request $request)
    {
        $data = $request->validate([
            'from_year' => 'required|integer',
            'from_month' => 'required|integer',
            'to_year' => 'required|integer',
            'to_month' => 'required|integer',
            'copy_codes' => 'nullable|array',
        ]);

        $fromYear = (int)$data['from_year'];
        $fromMonth = (int)$data['from_month'];
        $toYear = (int)$data['to_year'];
        $toMonth = (int)$data['to_month'];
        $copyCodes = $data['copy_codes'] ?? null; // if null copy all editable

        $query = FinancialValue::where('year', $fromYear)->where('month', $fromMonth)
                 ->join('financial_line_items', 'financial_values.financial_line_item_id', '=', 'financial_line_items.id')
                 ->select('financial_values.*', 'financial_line_items.code');

        if (is_array($copyCodes) && count($copyCodes) > 0) {
            $query->whereIn('financial_line_items.code', $copyCodes);
        }

        $rows = $query->get();

        DB::beginTransaction();
        try {
            foreach ($rows as $r) {
                // find destination line id
                $line = FinancialLineItem::where('code', $r->code)->first();
                if (! $line) continue;

                // upsert to destination
                FinancialValue::updateOrCreate(
                    [
                        'financial_line_item_id' => $line->id,
                        'year' => $toYear,
                        'month' => $toMonth,
                    ],
                    [
                        'value' => (float)$r->value,
                    ]
                );
            }
            DB::commit();
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }


    
}
