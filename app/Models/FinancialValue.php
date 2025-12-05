<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialValue extends Model
{
    protected $fillable = [
        'year',
        'month',
        'value',
    ];
}
