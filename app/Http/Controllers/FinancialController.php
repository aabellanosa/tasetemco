<?php

namespace App\Http\Controllers;

use App\Models\FinancialFormulaMap;
use App\Models\FinancialLineItem;
use App\Services\FinancialRollOverService;
use App\Services\FinancialFormulaEvaluator;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function edit($year, $month)
    {
        $rollover = new FinancialRollOverService();

        $values = $rollover->loadValues($year, $month);

        $items = FinancialLineItem::orderBy('display_order')->get();

        $formulas = FinancialFormulaMap::all()->keyBy('line_item_id');

        return view('financial.edit', compact('year', 'month', 'items', 'values', 'formulas'));
    }

    public function update(Request $request, $year, $month)
    {
        $rollover = new FinancialRollOverService();

        $rollover->saveEditableValues($year, $month, $request->all());

        return redirect()
            ->back()
            ->with('success', 'Financial values updated!');
    }
}
