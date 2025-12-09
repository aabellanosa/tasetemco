<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\FinancialLineItem;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('audit:layout', function () {

    

    // 🔥 1. MASTER MAPPING (Locked)
    // You confirmed this entire structure earlier.
    // pdf_column = where number appears (2–4)
    // indent = visual level
    // is_editable = whether user enters value

    $expected = [

        // ----------------------
        // CURRENT ASSETS
        // ----------------------
        'current_assets_title' => [1,0,false],
        'cash_and_cash_equivalent' => [1,1,false],
        'cash_on_hand' => [4,2,true],
        'gcash_and_load' => [4,2,true],
        'lbp_ca' => [3,2,true],
        'lbp_savings' => [3,2,true],
        'dbp_savings' => [3,2,true],
        'one_coop' => [3,2,true],                // ← audit check #1
        'total_cash_and_cash_equiv' => [4,1,false],

        // Blank row = skipped

        'loan_receivable_title' => [1,1,false],
        'regular_loans' => [3,2,true],
        'associates' => [3,2,true],
        'micro_project' => [3,2,true],
        'past_due' => [2,2,true],
        'allowance_probable_losses' => [2,2,true], // multi-column derived
        'unearned_income' => [4,2,true],
        'total_loans_and_receivable' => [4,1,false],

        'advance_to_suppliers' => [4,1,true],
        'merch_inventory' => [4,1,true],
        'total_current_assets' => [4,1,false],

        // ----------------------
        // NON-CURRENT ASSETS
        // ----------------------
        'non_current_assets_title' => [1,0,false],
        'property_and_equipment' => [2,1,true],
        'acquisition_2023' => [2,1,true],
        'ppe_and_acq_total' => [2,1,false],
        'accumulated_depr' => [2,1,true],
        'ppe_net' => [3,1,false],

        'investment_pftec' => [2,1,true],
        'investment_climbs' => [2,1,true],
        'investment_ccb_cbss' => [2,1,true],
        'investment_total' => [3,1,false],

        'total_non_current_assets' => [4,0,false],
        'total_assets' => [4,0,false],

        // ----------------------
        // CURRENT LIABILITIES
        // ----------------------
        'current_liabilities_title' => [1,0,false],
        'savings_deposit' => [3,1,true],
        'due_to_union' => [3,1,true],
        'patronage_refund' => [3,1,true],
        'interest_on_share_capital' => [3,1,true],
        'total_current_liabilities' => [4,1,false],

        // ----------------------
        // NON-CURRENT LIABILITIES
        // ----------------------
        'retirement_fund' => [3,1,true],
        'revolving_fund' => [3,1,true],

        'loans_lbp' => [2,1,true],
        'loans_lgu' => [2,1,true],
        'loans_ccb_cbss' => [2,1,true],
        'loans_total' => [3,1,false],

        'total_non_current_liabilities' => [4,1,false],
        'total_liabilities' => [4,0,false],

        // ----------------------
        // EQUITY
        // ----------------------
        'equity_title' => [1,0,false],
        'share_capital' => [3,1,true],
        'grant_capital' => [3,1,true],

        'stat_fund_title' => [1,1,false],
        'reserve_fund' => [2,2,true],
        'education_fund' => [2,2,true],
        'community_fund' => [2,2,true],
        'optional_fund' => [2,2,true],
        'stat_fund_total' => [3,1,false],

        'total_equity' => [4,0,false],
        'total_liabilities_equity' => [4,0,false],
    ];

    // -----------------------------
    // 2. Pull all DB rows
    // -----------------------------
    $db = FinancialLineItem::all()->keyBy('code');

    // -----------------------------
    // 3. Run audit
    // -----------------------------
    $this->info("==== PDF COLUMN AUDIT ====");

    foreach ($expected as $code => [$pdf, $indent, $editable]) {

        if (!isset($db[$code])) {
            $this->error("Missing in DB: $code");
            continue;
        }

        $row = $db[$code];
        $errors = [];

        if ($row->pdf_column != $pdf) $errors[] = "pdf_column {$row->pdf_column} ≠ $pdf";
        if ($row->indent != $indent) $errors[] = "indent {$row->indent} ≠ $indent";
        if ((int)$row->is_editable != (int)$editable)
            $errors[] = "is_editable {$row->is_editable} ≠ $editable";

        if (empty($errors)) {
            $this->info("✔ $code OK");
        } else {
            $this->error("❌ $code:");
            foreach ($errors as $err) $this->comment("   - $err");
        }
    }

    // -----------------------------
    // 4. Check DB for extra items
    // -----------------------------
    foreach ($db as $code => $row) {
        if (!isset($expected[$code])) {
            $this->warn("⚠ EXTRA in DB not in PDF: $code");
        }
    }

    $this->info("==== AUDIT COMPLETE ====");
});
