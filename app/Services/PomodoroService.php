<?php

namespace App\Services;

use App\Models\PomodoroLog;
use Auth;

class PomodoroService
{
  public function recordSession($seconds, $description)
  {
    return Auth::user()->pomodoros()->create([
      'duration' => $seconds,
      'description' => $description,
      'date' => now()
    ])->count();
  }
}