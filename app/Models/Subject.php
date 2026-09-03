<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Schedule;

class Subject extends Model
{
    public function task() {
        return $this->hasMany(Task::class);
    }
    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
}
