<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialLineItem;
use App\Models\FinancialValue;

class FinancialValuesJanuary2025Seeder extends Seeder
{
    public function run(): void
    {
        // January 2025 editable values extracted from the new PDF template
        $values = [

            // ======================
            // ASSETS
            // ======================

            // Cash & Cash Equivalent
            'cash_on_hand'                  => 164544.00,
            'gcash_and_load'                => 61238.00,
            'cash_in_bank_savings'          => 1261304.00,
            'cash_in_bank_checking'         => 230036.00,

            // Other Current Assets
            'regular_loans'                 => 8841436.69,
            'associates'                    => 691786.53,
            'micro_project'                 => 1892651.26,
            'past_due'                      => 307614.66,
            'allowance_for_probable_losses' => 25105.00,
            'merchandise_inventory'         => 169611.84,
            'bio_assets'                    => 0.00,
            'advance_to_suppliers'          => 403800.00,

            // Non-Current Assets
            'property_and_equipment'        => 2657322.59,
            'acquisition_2023'              => 0.00,
            'accumulated_depreciation'      => 13681.56,

            'investment_pftech'             => 176442.00,
            'investment_climbs'             => 100000.00,
            'investment_ccb_cbss'           => 104000.00,

            // ======================
            // LIABILITIES
            // ======================

            // Current Liabilities
            'savings_deposit'                               => 4128361.00,
            'due_to_union_federation'                       => 174107.00,
            'other_peso_savings'                            => 655852.00,
            'interest_on_share_capital_and_refund_payable'  => 0.00,
            'unearned_interest_income'                      => 2000.00,

            // Non-Current Liabilities
            'retirement_fund_payable'   => 755104.00,
            'loans_payable_lbp'         => 2668036.75,
            'loans_payable_lgu'         => 0.00,
            'loans_payable_ccb_cbss'    => 0.00,

            // ======================
            // EQUITY
            // ======================

            'share_capital'              => 6001321.00,
            'grant_capital'              => 600000.00,
            'reserve_fund'               => 1252065.00,
            'education_and_training_fund'=> 67344.00,
            'community_development_fund' => 27798.00,
            'optional_fund'              => 270779.00,
        ];

        foreach ($values as $code => $amount) {
            $item = FinancialLineItem::where('code', $code)->first();

            if (!$item) {
                echo "❌ Missing metadata for line-item code: {$code}\n";
                continue;
            }

            FinancialValue::updateOrCreate(
                [
                    'line_item_id' => $item->id,
                    'year' => 2025,
                    'month' => 1,
                ],
                [
                    'value' => $amount,
                ]
            );
        }

        echo "✅ January 2025 editable values inserted successfully.\n";
    }
}
