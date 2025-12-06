<?php

namespace Tests\Unit;

use App\Models\FinancialLineItem;
use App\Models\FinancialValue;
use App\Models\FinancialFormulaMap;
use App\Services\FinancialFormulaEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use RuntimeException;

class FinancialFormulaEvaluatorTest extends TestCase
{
    use RefreshDatabase;

    protected FinancialFormulaEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new FinancialFormulaEvaluator();
    }

    /** @test */
    public function it_returns_editable_value_from_database()
    {
        $item = FinancialLineItem::create([
            'code' => 'cash',
            'name' => 'Cash',
            'display_order' => 1,
            'is_editable' => true,
        ]);

        FinancialValue::create([
            'line_item_id' => $item->id,
            'year' => 2025,
            'month' => 1,
            'value' => 1234.56
        ]);

        $result = $this->evaluator->evaluateLineItem('cash', 2025, 1);

        $this->assertEquals(1234.56, $result);
    }

    /** @test */
    public function it_returns_zero_if_editable_value_missing()
    {
        FinancialLineItem::create([
            'code' => 'missing_value',
            'name' => 'Missing Value Row',
            'display_order' => 2,
            'is_editable' => true,
        ]);

        $result = $this->evaluator->evaluateLineItem('missing_value', 2025, 1);

        $this->assertEquals(0.0, $result);
    }

    /** @test */
    public function it_evaluates_simple_derived_formula()
    {
        $a = FinancialLineItem::create([
            'code' => 'a',
            'name' => 'A',
            'display_order' => 1,
            'is_editable' => true,
        ]);

        $b = FinancialLineItem::create([
            'code' => 'b',
            'name' => 'B',
            'display_order' => 2,
            'is_editable' => true,
        ]);

        FinancialValue::create([
            'line_item_id' => $a->id,
            'year' => 2025,
            'month' => 1,
            'value' => 10,
        ]);

        FinancialValue::create([
            'line_item_id' => $b->id,
            'year' => 2025,
            'month' => 1,
            'value' => 5,
        ]);

        $c = FinancialLineItem::create([
            'code' => 'c',
            'name' => 'C',
            'display_order' => 3,
            'is_editable' => false,
        ]);

        FinancialFormulaMap::create([
            'line_item_id' => $c->id,
            'formula' => 'a + b',
        ]);

        $result = $this->evaluator->evaluateLineItem('c', 2025, 1);

        $this->assertEquals(15, $result);
    }

    /** @test */
    public function it_evaluates_nested_derived_values()
    {
        $a = FinancialLineItem::create([
            'code' => 'a',
            'name' => 'A',
            'display_order' => 1,
            'is_editable' => true,
        ]);

        $b = FinancialLineItem::create([
            'code' => 'b',
            'name' => 'B',
            'display_order' => 2,
            'is_editable' => false,
        ]);

        $c = FinancialLineItem::create([
            'code' => 'c',
            'name' => 'C',
            'display_order' => 3,
            'is_editable' => false,
        ]);

        FinancialValue::create([
            'line_item_id' => $a->id,
            'year' => 2025,
            'month' => 1,
            'value' => 100,
        ]);

        FinancialFormulaMap::create([
            'line_item_id' => $b->id,
            'formula' => 'a - 30',
        ]);

        FinancialFormulaMap::create([
            'line_item_id' => $c->id,
            'formula' => 'b + 20',
        ]);

        $result = $this->evaluator->evaluateLineItem('c', 2025, 1);

        // b = 100 - 30 = 70
        // c = 70 + 20 = 90
        $this->assertEquals(90, $result);
    }

    /** @test */
    public function it_throws_error_if_formula_missing_for_derived_line()
    {
        $x = FinancialLineItem::create([
            'code' => 'x',
            'name' => 'X',
            'display_order' => 1,
            'is_editable' => false,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing formula');

        $this->evaluator->evaluateLineItem('x', 2025, 1);
    }

    /** @test */
    public function it_detects_circular_references()
    {
        $a = FinancialLineItem::create([
            'code' => 'a',
            'name' => 'A',
            'display_order' => 1,
            'is_editable' => false,
        ]);

        $b = FinancialLineItem::create([
            'code' => 'b',
            'name' => 'B',
            'display_order' => 2,
            'is_editable' => false,
        ]);

        FinancialFormulaMap::create([
            'line_item_id' => $a->id,
            'formula' => 'b + 1',
        ]);

        FinancialFormulaMap::create([
            'line_item_id' => $b->id,
            'formula' => 'a + 1',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Circular');

        $this->evaluator->evaluateLineItem('a', 2025, 1);
    }
}
