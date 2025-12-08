<?php

namespace App\Services;

use App\Models\FinancialLineItem;
use App\Models\FinancialValue;
use App\Services\FinancialFormulaEvaluator;

class FinancialRollOverService
{
    public function loadValues(int $year, int $month): array
    {
        $result = [];

        $items = FinancialLineItem::orderBy('display_order')->get();
        $evaluator = new FinancialFormulaEvaluator();

        $previousMonth = $month - 1;
        $previousYear = $year;

        if ($previousMonth === 0) {
            $previousMonth = 12;
            $previousYear--;
        }

        foreach ($items as $item) {
            if ($item->is_editable) {
                // First try current month saved value
                $current = FinancialValue::where('line_item_id', $item->id)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->first();

                if ($current) {
                    $result[$item->code] = $current->value;
                    continue;
                }

                // Else fallback to previous month computed
                $result[$item->code] =
                    $evaluator->evaluateLineItem($item->code, $previousYear, $previousMonth);
            } else {
                // Derived: always compute live
                $result[$item->code] = $evaluator->evaluateLineItem($item->code, $year, $month);
            }
        }

        return $result;
    }

    public function saveEditableValues(int $year, int $month, array $inputs): void
    {
        $items = FinancialLineItem::where('is_editable', true)->get();

        foreach ($items as $item) {
            $value = $inputs[$item->code] ?? 0;

            FinancialValue::updateOrCreate(
                [
                    'line_item_id' => $item->id,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'value' => $value
                ]
            );
        }
    }
}
