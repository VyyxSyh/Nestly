<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'deadline',
        'status',
        'progress_mode',
        'progress',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function checklistItems()
    {
        return $this->hasMany(TaskChecklistItem::class);
    }

    public function getPriorityAttribute(): string
    {
        if ($this->progress >= 100) {
            return 'done';
        }

        if (Carbon::now()->greaterThan($this->deadline)) {
            return 'overdue';
        }

        $daysLeft = Carbon::now()->diffInDays($this->deadline, false);

        if ($daysLeft <= 10) {
            return 'high';
        }

        if ($daysLeft <= 30) {
            return 'medium';
        }

        return 'low';
    }

    public function getUrgencyColorAttribute(): string
    {
        return match ($this->priority) {
            'done' => 'gray',
            'overdue' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            default => 'green',
        };
    }
}