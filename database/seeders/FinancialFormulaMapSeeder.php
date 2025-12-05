<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialLineItem;
use App\Models\FinancialFormulaMap;

class FinancialFormulaMapSeeder extends Seeder
{
    public function run(): void
    {
        $formulas = [

            // ============================
            // 1. ASSETS
            // ============================

            // --- Cash & Cash Equivalent ---
            'cash_equivalent_total' => 
                'cash_on_hand + gcash_and_load + cash_in_bank_savings + cash_in_bank_checking',

            // --- Other Current Assets ---
            'past_due_adjusted' => 
                'past_due - allowance_for_probable_losses',

            'total_loan_receivables_past_adjustments' =>
                'regular_loans + associates + micro_project + past_due_adjusted',

            'total_current_assets' =>
                'cash_equivalent_total + total_loan_receivables_past_adjustments + merchandise_inventory + bio_assets + advance_to_suppliers',

            // --- Non-Current Assets ---
            'property_and_equipment_net' =>
                'property_and_equipment + acquisition_2023 - accumulated_depreciation',

            'investments_total' =>
                'investment_pftech + investment_climbs + investment_ccb_cbss',

            'total_non_current_assets' =>
                'property_and_equipment_net + investments_total',

            // --- TOTAL ASSETS ---
            'total_assets' =>
                'total_current_assets + total_non_current_assets',


            // ============================
            // 2. LIABILITIES
            // ============================

            // --- Current Liabilities ---
            'total_current_liabilities' =>
                'savings_deposit + due_to_union_federation + other_peso_savings + interest_on_share_capital_and_refund_payable + unearned_interest_income',

            // --- Non-Current Liabilities ---
            'total_loans_payable' =>
                'loans_payable_lbp + loans_payable_lgu + loans_payable_ccb_cbss',

            'total_non_current_liabilities' =>
                'retirement_fund_payable + total_loans_payable',

            // --- TOTAL LIABILITIES ---
            'total_liabilities' =>
                'total_current_liabilities + total_non_current_liabilities',


            // ============================
            // 3. EQUITY
            // ============================

            'total_statutory_fund' =>
                'reserve_fund + education_and_training_fund + community_development_fund + optional_fund',

            'total_equity' =>
                'share_capital + grant_capital + total_statutory_fund',

            // ============================
            // GRAND TOTAL VALIDATION
            // ============================

            'total_liabilities_and_equity' =>
                'total_liabilities + total_equity',
        ];

        foreach ($formulas as $code => $formula) {
            $item = FinancialLineItem::where('code', $code)->first();

            if (!$item) {
                echo "❌ Missing line-item code in metadata: {$code}\n";
                continue;
            }

            FinancialFormulaMap::updateOrCreate(
                ['line_item_id' => $item->id],
                ['formula' => $formula]
            );
        }

        echo "✅ Formula Map Seeder executed successfully.\n";
    }
}
