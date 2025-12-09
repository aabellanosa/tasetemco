<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FinancialLineItem;

class FinancialFormulaMap extends Model
{
    protected $fillable = [
        'financial_line_item_id',
        'formula',
    ];

    public function lineItem()
    {
        return $this->belongsTo(FinancialLineItem::class, 'financial_line_item_id');    
    }
}
