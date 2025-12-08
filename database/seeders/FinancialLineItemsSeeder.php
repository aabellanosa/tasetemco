<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialLineItem;

class FinancialLineItemsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [

            //-------------------------------
            // CURRENT ASSETS
            //-------------------------------
            ['code' => 'current_assets_title', 'label' => 'CURRENT ASSETS:', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            ['code' => 'cash_and_equivalent_title', 'label' => 'CASH AND CASH EQUIVALENT', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 1],

            ['code' => 'cash_on_hand', 'label' => 'CASH ON HAND', 'pdf_column' => 4, 'is_editable' => true, 'indent' => 2],
            ['code' => 'gcash_load', 'label' => 'GCASH & LOAD', 'pdf_column' => 4, 'is_editable' => true, 'indent' => 2],

            ['code' => 'lbp_ca', 'label' => 'CASH IN BANK LBP C/A', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 2],
            ['code' => 'lbp_savings', 'label' => 'LBP SAVINGS', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 3],
            ['code' => 'dbp_savings', 'label' => 'DBP SAVINGS', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 3],

            ['code' => 'one_coop', 'label' => 'ONE COOP (former CCB)', 'pdf_column' => 4, 'is_editable' => true, 'indent' => 3],

            ['code' => 'total_cash_equivalents', 'label' => 'TOTAL (CASH AND CASH EQUIVALENTS)', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 2],

            // spacer row
            ['code' => 'spacer_1', 'label' => '', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            //-------------------------------
            // LOAN RECEIVABLES
            //-------------------------------
            ['code' => 'loan_receivable_title', 'label' => 'LOAN RECEIVABLE:', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 1],

            ['code' => 'regular_loans', 'label' => 'REGULAR LOANS (See attached Schedule)', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 2],
            ['code' => 'associates', 'label' => 'ASSOCIATES', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 2],
            ['code' => 'micro_project', 'label' => 'MICRO PROJECT', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 2],

            ['code' => 'past_due', 'label' => 'PAST DUE', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],

            ['code' => 'allowance_probable', 'label' => 'LESS: ALLOWANCE FOR PROBABLE LOSSES', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],

            ['code' => 'unearned_income', 'label' => 'LESS: UNEARNED INCOME', 'pdf_column' => 4, 'is_editable' => true, 'indent' => 2],

            ['code' => 'spacer_2', 'label' => '', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            ['code' => 'total_loans_receivable', 'label' => 'TOTAL LOANS AND OTHER RECEIVABLES', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 2],

            ['code' => 'spacer_3', 'label' => '', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            //-------------------------------
            // ADVANCES & INVENTORY
            //-------------------------------
            ['code' => 'advance_suppliers', 'label' => 'ADVANCE TO SUPPLIERS', 'pdf_column' => 4, 'is_editable' => true, 'indent' => 1],
            ['code' => 'merch_inventory', 'label' => 'MERCHANDISE INVENTORY', 'pdf_column' => 4, 'is_editable' => true, 'indent' => 1],

            ['code' => 'total_current_assets', 'label' => 'TOTAL CURRENT ASSETS', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 1],

            ['code' => 'spacer_4', 'label' => '', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            //-------------------------------
            // NON-CURRENT ASSETS
            //-------------------------------
            ['code' => 'non_current_assets_title', 'label' => 'NON-CURRENT ASSETS', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            ['code' => 'ppe', 'label' => 'PROPERTY & EQUIPMENT', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 1],
            ['code' => 'acquisition_2023', 'label' => 'ADD: ACQUISITION, 2023', 'pdf_column' => 1, 'is_editable' => true, 'indent' => 2],
            ['code' => 'total_ppe_acquisition', 'label' => 'TOTAL PPE AND ACQUISITION', 'pdf_column' => 2, 'is_editable' => false, 'indent' => 1],
            ['code' => 'accum_depreciation', 'label' => 'LESS: ACCUMULATED DEPRECIATION', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 1],

            ['code' => 'spacer_5', 'label' => '', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            ['code' => 'ppe_net', 'label' => 'PROPERTY & EQUIPMENT, NET', 'pdf_column' => 3, 'is_editable' => false, 'indent' => 1],

            ['code' => 'investment_pftech', 'label' => 'INVESTMENT IN PFTEC', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 1],
            ['code' => 'investment_climbs', 'label' => 'IN CLIMBS', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],
            ['code' => 'investment_ccb_cbss', 'label' => 'IN CCB/CBSS', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],

            ['code' => 'total_non_current_assets', 'label' => 'TOTAL NON-CURRENT ASSETS', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 1],

            ['code' => 'total_assets', 'label' => 'TOTAL ASSETS', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 0],

            ['code' => 'spacer_6', 'label' => '', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            //-------------------------------
            // CURRENT LIABILITIES
            //-------------------------------
            ['code' => 'current_liabilities_title', 'label' => 'CURRENT LIABILITIES', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            ['code' => 'savings_deposit', 'label' => 'SAVINGS DEPOSIT (Pls see attched schedule)', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],
            ['code' => 'due_union_fed', 'label' => 'DUE TO UNION/FEDERATION', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],
            ['code' => 'patronage_refund', 'label' => 'PATRONAGE REFUND PAYABLE', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],
            ['code' => 'interest_share_capital', 'label' => 'INTEREST ON SHARE CAPITAL & REFUND PAYABLE', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],

            ['code' => 'total_current_liabilities', 'label' => 'TOTAL CURRENT LIABILITIES', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 1],

            ['code' => 'spacer_7', 'label' => '', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            //-------------------------------
            // NON-CURRENT LIABILITIES
            //-------------------------------
            ['code' => 'retirement_fund', 'label' => 'RETIREMENT FUND PAYABLE', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],
            ['code' => 'revolving_fund', 'label' => 'REVOLVING FUNDS PAYABLE', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],

            ['code' => 'loans_lbp', 'label' => 'LOANS PAYABLE - LBP', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 1],
            ['code' => 'loans_lgu', 'label' => 'LGU', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],
            ['code' => 'loans_ccb_cbss', 'label' => 'CCB/CBSS', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],

            ['code' => 'total_non_current_liabilities', 'label' => 'TOTAL NON-CURRENT LIABILITIES', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 1],

            ['code' => 'total_liabilities', 'label' => 'TOTAL LIABILITIES', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 0],

            //-------------------------------
            // EQUITY
            //-------------------------------
            ['code' => 'equity_title', 'label' => 'EQUITY', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 0],

            ['code' => 'share_capital', 'label' => 'SHARE CAPITAL (PLS. SEE ATT.SCHEDULE)', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],
            ['code' => 'grant_capital', 'label' => 'GRANT CAPITAL', 'pdf_column' => 3, 'is_editable' => true, 'indent' => 1],

            ['code' => 'statutory_fund_title', 'label' => 'STATUTORY FUND:', 'pdf_column' => 1, 'is_editable' => false, 'indent' => 1],

            ['code' => 'reserve_fund', 'label' => 'RESERVE FUND', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],
            ['code' => 'education_training', 'label' => 'EDUCATION & TRAINING FUND', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],
            ['code' => 'community_dev', 'label' => 'COMMUNITY DEVELOPMENT FUND', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],
            ['code' => 'optional_fund', 'label' => 'OPTIONAL FUND', 'pdf_column' => 2, 'is_editable' => true, 'indent' => 2],

            ['code' => 'total_equity', 'label' => 'TOTAL EQUITY', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 1],

            ['code' => 'total_liabilities_equity', 'label' => 'TOTAL LIABILITIES & EQUITY', 'pdf_column' => 4, 'is_editable' => false, 'indent' => 0],

        ];

        $order = 1;
        foreach ($items as $item) {
            FinancialLineItem::create([
                'code'          => $item['code'],
                'label'         => $item['label'],
                'display_order' => $order++,
                'pdf_column'    => $item['pdf_column'],
                'indent_level'  => $item['indent'],
                'is_editable'   => $item['is_editable'],
            ]);
        }
    }
}
