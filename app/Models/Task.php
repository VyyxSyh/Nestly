<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'deadline',
        'status',
        'priority',
        'progress_mode',
        'progress'
    ];
    public function subject() {
        return $this->belongsTo(Subject::class);
    }
    public function checklistItems() {
        return $this->hasMany(TaskChecklistItem::class);
    }
}
