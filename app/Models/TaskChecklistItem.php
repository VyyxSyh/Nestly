<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklistItem extends Model
{
    protected $fillable = ['task_id', 'title', 'is_done'];
    public function task() {
        return $this->belongsTo(Task::class);
    }
}
