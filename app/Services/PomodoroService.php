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
      'description' => $description ?? 'Pomodoro',
      'date' => now()
    ])->count();
  }

  public function showLogs(){
    return Auth::user()
    ->pomodoros()
    ->latest()
    ->limit(10)
    ->get();
  }
}