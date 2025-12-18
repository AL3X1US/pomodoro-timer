<?php

namespace App\Services;

use App\Models\PomodoroLog;
use Auth;

class PomodoroService
{
  public function recordSession($seconds, $description)
  {
    Auth::user()->pomodoros()->create([
      'duration' => $seconds,
      'description' => $description,
      'date' => now()
    ]);

    return true;
  }

  public function getUserLogs($userId)
  {
    return PomodoroLog::where('user_id', $userId)->get();
  }
}