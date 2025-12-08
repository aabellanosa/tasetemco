<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialLineItem;
use App\Models\FinancialValue;

class FinancialValuesJanuary2025Seeder extends Seeder
{
    public function run(): void
    {
        $year = 2025;
        $month = 1;

        // CODE → VALUE (editable only)
        $data = [

            // CURRENT ASSETS (Cash)
            'cash_on_hand'      => 164544,
            'gcash_load'        => 61238,
            'lbp_ca'            => 1244183,
            'lbp_savings'       => 125075,
            'dbp_savings'       => 7566,
            'one_coop'          => 412980,

            // LOAN RECEIVABLES
            'regular_loans'     => 8686226,
            'associates'        => 746162,
            'micro_project'     => 1606977,
            'past_due'          => 578988,
            'allowance_probable'=> 287087,
            'unearned_income'   => 1174809,

            // INVENTORY & ADVANCES
            'advance_suppliers' => 34811,
            'merch_inventory'   => 142377,

            // NON-CURRENT ASSETS
            'ppe'               => 2379333,
            'acquisition_2023'  => 0,
            'accum_depreciation'=> 1166517,
            'investment_pftech' => 176442,
            'investment_climbs' => 25000,
            'investment_ccb_cbss' => 104000,

            // CURRENT LIABILITIES
            'savings_deposit'   => 1336324,
            'due_union_fed'     => 113337,
            'patronage_refund'  => 194589,
            'interest_share_capital' => 454041,

            // NON-CURRENT LIABILITIES
            'retirement_fund'   => 755104,
            'revolving_fund'    => 126750,
            'loans_lbp'         => 2668037,
            'loans_lgu'         => 0,
            'loans_ccb_cbss'    => 0,

            // EQUITY
            'share_capital'     => 6001321,
            'grant_capital'     => 600000,
            'reserve_fund'      => 1252065,
            'education_training'=> 67344,
            'community_dev'     => 27798,
            'optional_fund'     => 270779,
        ];

        foreach ($data as $code => $amount) {

            $item = FinancialLineItem::where('code', $code)->first();

            if (!$item) {
                throw new \Exception("Line item '$code' not found in financial_line_items.");
            }

            FinancialValue::create([
                'financial_line_item_id' => $item->id,
                'year'   => $year,
                'month'  => $month,
                'value'  => $amount,
            ]);
        }
    }
}
