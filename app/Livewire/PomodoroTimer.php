<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\PomodoroService;


class PomodoroTimer extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->count = auth()->user()->pomodoros()->count();
    }

    public function incrementPomodoro($seconds, $description, PomodoroService $service)
    {
        $this->count = $service->recordSession($seconds, $description);
    }
    public function render()
    {
        return view('livewire.pomodoro-timer');
    }
}
