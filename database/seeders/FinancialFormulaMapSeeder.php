<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialLineItem;
use App\Models\FinancialFormulaMap;

class FinancialFormulaMapSeeder extends Seeder
{
    public function run(): void
    {
        // helper to resolve FK by code
        $id = fn($code) =>
            FinancialLineItem::where('code', $code)->value('id')
            ?? throw new \Exception("Line item code '$code' not found.");

        $formulas = [

            //----------------------------------------------------------
            // CURRENT ASSETS → CASH & CASH EQUIVALENTS
            //----------------------------------------------------------
            [
                'code'     => 'total_cash_equivalents',
                'formula'  => 'cash_on_hand + gcash_load + lbp_ca + lbp_savings + dbp_savings + one_coop',
            ],

            //----------------------------------------------------------
            // LOAN RECEIVABLES
            //----------------------------------------------------------
            [
                'code'     => 'total_loans_receivable',
                'formula'  =>
                    'regular_loans + associates + micro_project + past_due - allowance_probable - unearned_income',
            ],

            //----------------------------------------------------------
            // TOTAL CURRENT ASSETS
            //----------------------------------------------------------
            [
                'code'     => 'total_current_assets',
                'formula'  =>
                    'total_cash_equivalents + total_loans_receivable + advance_suppliers + merch_inventory',
            ],

            //----------------------------------------------------------
            // NON-CURRENT ASSETS → PPE
            //----------------------------------------------------------
            [
                'code'     => 'total_ppe_acquisition',
                'formula'  => 'ppe + acquisition_2023',
            ],

            [
                'code'     => 'ppe_net',
                'formula'  => 'total_ppe_acquisition - accum_depreciation',
            ],

            //----------------------------------------------------------
            // NON-CURRENT ASSETS → INVESTMENTS
            //----------------------------------------------------------
            [
                'code'     => 'total_non_current_assets',
                'formula'  =>
                    'ppe_net + investment_pftech + investment_climbs + investment_ccb_cbss',
            ],

            //----------------------------------------------------------
            // TOTAL ASSETS
            //----------------------------------------------------------
            [
                'code'     => 'total_assets',
                'formula'  =>
                    'total_current_assets + total_non_current_assets',
            ],

            //----------------------------------------------------------
            // CURRENT LIABILITIES
            //----------------------------------------------------------
            [
                'code'     => 'total_current_liabilities',
                'formula'  =>
                    'savings_deposit + due_union_fed + patronage_refund + interest_share_capital',
            ],

            //----------------------------------------------------------
            // NON-CURRENT LIABILITIES
            //----------------------------------------------------------
            [
                'code'     => 'total_non_current_liabilities',
                'formula'  =>
                    'retirement_fund + revolving_fund + loans_lbp + loans_lgu + loans_ccb_cbss',
            ],

            //----------------------------------------------------------
            // TOTAL LIABILITIES
            //----------------------------------------------------------
            [
                'code'     => 'total_liabilities',
                'formula'  =>
                    'total_current_liabilities + total_non_current_liabilities',
            ],

            //----------------------------------------------------------
            // EQUITY → STATUTORY FUNDS
            //----------------------------------------------------------
            [
                'code'     => 'total_equity',
                'formula'  =>
                    'share_capital + grant_capital + reserve_fund + education_training + community_dev + optional_fund',
            ],

            //----------------------------------------------------------
            // TOTAL LIABILITIES & EQUITY
            //----------------------------------------------------------
            [
                'code'     => 'total_liabilities_equity',
                'formula'  =>
                    'total_liabilities + total_equity',
            ],
        ];

        foreach ($formulas as $f) {
            FinancialFormulaMap::create([
                'financial_line_item_id' => $id($f['code']),
                'formula' => $f['formula'],
            ]);
        }
    }
}
