<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'accent_color'];
    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
}
