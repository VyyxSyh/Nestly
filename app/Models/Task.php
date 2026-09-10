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

    public function getStatusAttribute(): string
    {
        if ($this->progress >= 100) {
            return 'completed';
        }

        if ($this->progress > 0) {
            return 'in_progress';
        }

        return 'not_started';
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

        if ($daysLeft <= 5) {
            return 'critical';
        }

        if ($daysLeft <= 12) {
            return 'urgent';
        }

        if ($daysLeft <= 20) {
            return 'approaching';
        }

        return 'safe';
    }

    public function getUrgencyColorAttribute(): string
    {
        return match ($this->priority) {
            'done' => 'gray',
            'overdue', 'critical' => 'red',
            'urgent' => 'orange',
            'approaching' => 'yellow',
            default => 'green',
        };
    }
}