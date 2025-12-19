<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\PomodoroService;
use Log;



class PomodoroTimer extends Component
{
    public $count = 0;
    public $logs = [];


    public function mount()
    {
        $this->count = auth()->user()->pomodoros()->count();
    }

    public function incrementPomodoro($seconds, $description, PomodoroService $service)
    {
        $this->count = $service->recordSession($seconds, $description);
    }
    public function showLogs(PomodoroService $service)
    {
        Log::debug('Showing logs');
        $this->logs = $service->showLogs();
    }
    public function render()
    {
        return view('livewire.pomodoro-timer');
    }

}
