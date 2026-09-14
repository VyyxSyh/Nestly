<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'month',
        'year',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}