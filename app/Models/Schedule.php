<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'subject_id',
        'user_id',
        'day',
        'start_time',
        'end_time',
        'room',
        'lecturer',
        'accent_color',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
