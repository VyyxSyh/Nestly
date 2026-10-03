<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'subject_id',
        'user_id',
        'title',
        'description',
        'deadline',
        'deadline_time',
        'progress_mode',
        'progress',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(TaskChecklistItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

        if (Carbon::now()->startOfDay()->greaterThan($this->deadline)) {
            return 'overdue';
        }

        $daysLeft = Carbon::now()->startOfDay()->diffInDays($this->deadline, false);

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

    public function getDeadlineFormattedAttribute(): string
    {
        $formatted = $this->deadline->translatedFormat('l, d F Y');
        if ($this->deadline_time) {
            $formatted .= ', '.Carbon::parse($this->deadline_time)->format('H:i');
        }

        return $formatted;
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
