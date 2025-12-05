<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialLineItem;

class FinancialLineItemsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [

            // ============================
            // 1. ASSETS
            // ============================

            // --- 1.1 CASH & CASH EQUIVALENT ---
            [
                'code' => 'cash_on_hand',
                'label' => 'Cash on Hand',
                'section' => 'assets',
                'sub_section' => 'cash_and_cash_equivalent',
                'is_editable' => true,
                'display_order' => 1,
            ],
            [
                'code' => 'gcash_and_load',
                'label' => 'GCash & Load',
                'section' => 'assets',
                'sub_section' => 'cash_and_cash_equivalent',
                'is_editable' => true,
                'display_order' => 2,
            ],
            [
                'code' => 'cash_in_bank_savings',
                'label' => 'Cash in Bank - Savings',
                'section' => 'assets',
                'sub_section' => 'cash_and_cash_equivalent',
                'is_editable' => true,
                'display_order' => 3,
            ],
            [
                'code' => 'cash_in_bank_checking',
                'label' => 'Cash in Bank - Checking',
                'section' => 'assets',
                'sub_section' => 'cash_and_cash_equivalent',
                'is_editable' => true,
                'display_order' => 4,
            ],
            [
                'code' => 'cash_equivalent_total',
                'label' => 'Total Cash & Cash Equivalent',
                'section' => 'assets',
                'sub_section' => 'cash_and_cash_equivalent',
                'is_editable' => false,
                'display_order' => 5,
            ],

            // --- 1.2 OTHER CURRENT ASSETS ---
            [
                'code' => 'regular_loans',
                'label' => 'Regular Loans',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 6,
            ],
            [
                'code' => 'associates',
                'label' => 'Associates',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 7,
            ],
            [
                'code' => 'micro_project',
                'label' => 'Micro Project',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 8,
            ],
            [
                'code' => 'past_due',
                'label' => 'Past Due',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 9,
            ],
            [
                'code' => 'allowance_for_probable_losses',
                'label' => 'Allowance for Probable Losses',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 10,
            ],
            [
                'code' => 'past_due_adjusted',
                'label' => 'Past Due (Net of Allowance)',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => false,
                'display_order' => 11,
            ],
            [
                'code' => 'total_loan_receivables_past_adjustments',
                'label' => 'Total Loan Receivables (Adjusted)',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => false,
                'display_order' => 12,
            ],

            [
                'code' => 'merchandise_inventory',
                'label' => 'Merchandise Inventory',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 13,
            ],
            [
                'code' => 'bio_assets',
                'label' => 'Bio Assets',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 14,
            ],
            [
                'code' => 'advance_to_suppliers',
                'label' => 'Advance to Suppliers',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => true,
                'display_order' => 15,
            ],

            [
                'code' => 'total_current_assets',
                'label' => 'Total Current Assets',
                'section' => 'assets',
                'sub_section' => 'other_current_assets',
                'is_editable' => false,
                'display_order' => 16,
            ],

            // --- 1.3 NON-CURRENT ASSETS ---
            [
                'code' => 'property_and_equipment',
                'label' => 'Property & Equipment',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => true,
                'display_order' => 17,
            ],
            [
                'code' => 'acquisition_2023',
                'label' => 'Acquisition (2023)',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => true,
                'display_order' => 18,
            ],
            [
                'code' => 'accumulated_depreciation',
                'label' => 'Less: Accumulated Depreciation',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => true,
                'display_order' => 19,
            ],
            [
                'code' => 'property_and_equipment_net',
                'label' => 'Net PPE',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => false,
                'display_order' => 20,
            ],

            [
                'code' => 'investment_pftech',
                'label' => 'Investment - PFTECH',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => true,
                'display_order' => 21,
            ],
            [
                'code' => 'investment_climbs',
                'label' => 'Investment - CLIMBS',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => true,
                'display_order' => 22,
            ],
            [
                'code' => 'investment_ccb_cbss',
                'label' => 'Investment - CCB/CBSS',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => true,
                'display_order' => 23,
            ],
            [
                'code' => 'investments_total',
                'label' => 'Total Investments',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => false,
                'display_order' => 24,
            ],

            [
                'code' => 'total_non_current_assets',
                'label' => 'Total Non-Current Assets',
                'section' => 'assets',
                'sub_section' => 'non_current_assets',
                'is_editable' => false,
                'display_order' => 25,
            ],

            [
                'code' => 'total_assets',
                'label' => 'TOTAL ASSETS',
                'section' => 'assets',
                'sub_section' => null,
                'is_editable' => false,
                'display_order' => 26,
            ],

            // ============================
            // 2. LIABILITIES
            // ============================

            // --- 2.1 CURRENT LIABILITIES ---
            [
                'code' => 'savings_deposit',
                'label' => 'Savings Deposit',
                'section' => 'liabilities',
                'sub_section' => 'current_liabilities',
                'is_editable' => true,
                'display_order' => 27,
            ],
            [
                'code' => 'due_to_union_federation',
                'label' => 'Due to Union / Federation',
                'section' => 'liabilities',
                'sub_section' => 'current_liabilities',
                'is_editable' => true,
                'display_order' => 28,
            ],
            [
                'code' => 'other_peso_savings',
                'label' => 'Other Peso Savings',
                'section' => 'liabilities',
                'sub_section' => 'current_liabilities',
                'is_editable' => true,
                'display_order' => 29,
            ],
            [
                'code' => 'interest_on_share_capital_and_refund_payable',
                'label' => 'Interest on Share Capital and Refund Payable',
                'section' => 'liabilities',
                'sub_section' => 'current_liabilities',
                'is_editable' => true,
                'display_order' => 30,
            ],
            [
                'code' => 'unearned_interest_income',
                'label' => 'Unearned Interest Income',
                'section' => 'liabilities',
                'sub_section' => 'current_liabilities',
                'is_editable' => true,
                'display_order' => 31,
            ],

            [
                'code' => 'total_current_liabilities',
                'label' => 'Total Current Liabilities',
                'section' => 'liabilities',
                'sub_section' => 'current_liabilities',
                'is_editable' => false,
                'display_order' => 32,
            ],

            // --- 2.2 NON-CURRENT LIABILITIES ---
            [
                'code' => 'retirement_fund_payable',
                'label' => 'Retirement Fund Payable',
                'section' => 'liabilities',
                'sub_section' => 'non_current_liabilities',
                'is_editable' => true,
                'display_order' => 33,
            ],
            [
                'code' => 'loans_payable_lbp',
                'label' => 'Loans Payable - LBP',
                'section' => 'liabilities',
                'sub_section' => 'non_current_liabilities',
                'is_editable' => true,
                'display_order' => 34,
            ],
            [
                'code' => 'loans_payable_lgu',
                'label' => 'Loans Payable - LGU',
                'section' => 'liabilities',
                'sub_section' => 'non_current_liabilities',
                'is_editable' => true,
                'display_order' => 35,
            ],
            [
                'code' => 'loans_payable_ccb_cbss',
                'label' => 'Loans Payable - CCB/CBSS',
                'section' => 'liabilities',
                'sub_section' => 'non_current_liabilities',
                'is_editable' => true,
                'display_order' => 36,
            ],
            [
                'code' => 'total_loans_payable',
                'label' => 'Total Loans Payable',
                'section' => 'liabilities',
                'sub_section' => 'non_current_liabilities',
                'is_editable' => false,
                'display_order' => 37,
            ],
            [
                'code' => 'total_non_current_liabilities',
                'label' => 'Total Non-Current Liabilities',
                'section' => 'liabilities',
                'sub_section' => 'non_current_liabilities',
                'is_editable' => false,
                'display_order' => 38,
            ],

            [
                'code' => 'total_liabilities',
                'label' => 'TOTAL LIABILITIES',
                'section' => 'liabilities',
                'sub_section' => null,
                'is_editable' => false,
                'display_order' => 39,
            ],

            // ============================
            // 3. EQUITY
            // ============================

            [
                'code' => 'share_capital',
                'label' => 'Share Capital',
                'section' => 'equity',
                'sub_section' => 'equity_main',
                'is_editable' => true,
                'display_order' => 40,
            ],
            [
                'code' => 'grant_capital',
                'label' => 'Grant Capital',
                'section' => 'equity',
                'sub_section' => 'equity_main',
                'is_editable' => true,
                'display_order' => 41,
            ],

            // --- Statutory Funds ---
            [
                'code' => 'reserve_fund',
                'label' => 'Reserve Fund',
                'section' => 'equity',
                'sub_section' => 'statutory_funds',
                'is_editable' => true,
                'display_order' => 42,
            ],
            [
                'code' => 'education_and_training_fund',
                'label' => 'Education & Training Fund',
                'section' => 'equity',
                'sub_section' => 'statutory_funds',
                'is_editable' => true,
                'display_order' => 43,
            ],
            [
                'code' => 'community_development_fund',
                'label' => 'Community Development Fund',
                'section' => 'equity',
                'sub_section' => 'statutory_funds',
                'is_editable' => true,
                'display_order' => 44,
            ],
            [
                'code' => 'optional_fund',
                'label' => 'Optional Fund',
                'section' => 'equity',
                'sub_section' => 'statutory_funds',
                'is_editable' => true,
                'display_order' => 45,
            ],
            [
                'code' => 'total_statutory_fund',
                'label' => 'Total Statutory Fund',
                'section' => 'equity',
                'sub_section' => 'statutory_funds',
                'is_editable' => false,
                'display_order' => 46,
            ],

            [
                'code' => 'total_equity',
                'label' => 'TOTAL EQUITY',
                'section' => 'equity',
                'sub_section' => null,
                'is_editable' => false,
                'display_order' => 47,
            ],

            [
                'code' => 'total_liabilities_and_equity',
                'label' => 'TOTAL LIABILITIES & EQUITY',
                'section' => 'equity',
                'sub_section' => null,
                'is_editable' => false,
                'display_order' => 48,
            ],
        ];

        foreach ($items as $item) {
            FinancialLineItem::create($item);
        }
    }
}
