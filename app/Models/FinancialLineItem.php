<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialLineItem extends Model
{
    protected $fillable = [
        'label',
        'section',
        'sub-section',
        'is_editable',
        'display_order',
    ];
}
