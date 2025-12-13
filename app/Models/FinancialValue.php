<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialValue extends Model
{
    protected $fillable = [
        'financial_line_item_id',
        'year',
        'month',
        'value',
    ];
}
