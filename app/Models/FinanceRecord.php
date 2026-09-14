<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceRecord extends Model
{
    protected $fillable = [
        'type',
        'category',
        'amount',
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}