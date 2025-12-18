<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PomodoroLog extends Model
{
    protected $table = "pomodoro_logs";

    protected $fillable = [
        'user_id',
        'description',
        'duration',
        'date_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
