@use('App\Enums\TimerPreset')

<div class="flex flex-col items-center justify-center p-8 shadow-xl bg-white rounded-3xl border border-gray-100" x-data="{ 


// 1. Memorizziamo lo stato del permesso ('default', 'granted', 'denied')
permission: Notification.permission,

// 2. Funzione per chiedere il permesso
requestPermission() {
  if (!('Notification' in window)) {
    alert('Il tuo browser non supporta le notifiche!');
    return;
  }

  Notification.requestPermission().then(result => {
    this.permission = result;
    if (result === 'granted') {
      new Notification('Notifiche attivate! 🍅');
    }
  });
},

currentTab: 'pomodoro',
shortBreakTime: 300,
longBreakTime: 900,
  
timeLeft: 1500, 
timer: null,
running: false,
paused: false,
timeSelected: 1500,
description: '',
manualCompleted: false,
isBreak: false,

init() {
  this.showLogs();
},

start() {
if(this.isBreak){
  this.startBreak(this.timeLeft);
  return;
}
  this.description = '';
  clearInterval(this.timer);
  this.isBreak = false;
  this.running = true;
  this.paused = false;
  this.timer = setInterval(() => {
    if (this.timeLeft > 0) this.timeLeft--;
      else this.finish();
  }, 1000);
},

startBreak($time){
  clearInterval(this.timer);
  this.isBreak = true;
  this.running = true;
  this.paused = false;
  this.timeSelected = $time;
  this.timeLeft = $time;
  this.description = 'Break';
  this.timer = setInterval(() => {
    if (this.timeLeft > 0) {
      this.timeLeft--;
    }
    else{
      this.sendNotify('Pausa completata!');
      this.reset();
    }
  }, 1000);
},

reset() {
  clearInterval(this.timer);
  this.currentTab = 'pomodoro';
  this.isBreak = false;
  this.running = false;
  this.paused = false;
  this.timeSelected = 1500;
  this.timeLeft = 1500;
  this.description = '';
},

pause() {
  this.running = false;
  this.paused = true;
  clearInterval(this.timer);
},

sendNotify($message) {
  if (this.permission === 'granted') {
    const notification = new Notification($message);
    notification.onclick = () => {
      window.focus();
      notification.close();
    };
  }
},

completed() {
  this.manualCompleted = true;
  this.finish();
},

finish() {
  clearInterval(this.timer);
  this.running = false;
  this.paused = false;
  this.isBreak = true;
  // notifica
  this.sendNotify('Pomodoro completato!');
  // Qui 'parliamo' con la classe PHP del Backend
  let tempoTrascorso = this.timeSelected - this.timeLeft;
  $wire.incrementPomodoro(tempoTrascorso, this.description);
  this.showLogs();
  if (this.manualCompleted) {
    this.manualCompleted = false;
    this.reset();
  }else{
    this.startBreak();
  }
},

  setTime(time) {
    this.description = '';
    this.timeSelected = time;
    this.timeLeft = time;
  },

  showLogs() {
    $wire.showLogs();
  }
}">

  <!-- tab notifiche -->
  <div x-show="permission === 'default'" x-cloak class="mb-6 text-center">
    <button @click="requestPermission()"
      class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-200 transition flex items-center justify-center gap-2 mx-auto">

      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
        </path>
      </svg>
      Abilita Notifiche Desktop
    </button>
  </div>

  <!-- tab corrente -->
  <div class="w-full max-w-2xl mt-10">

    <div class="flex border-b border-gray-200 mb-6">
      <button @click="currentTab = 'pomodoro' ; setTime(1500)"
        :class="currentTab === 'pomodoro' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
        Pomodoro
      </button>

      <button @click="currentTab = 'shortBreak'; startBreak(shortBreakTime)"
        :class="currentTab === 'shortBreak' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
        Short Break
      </button>

      <button x-cloak @click="currentTab = 'longBreak'; startBreak(longBreakTime)"
        :class="currentTab === 'longBreak' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
        Long Break
      </button>
    </div>

    <div class="justify-center text-center">

      <!-- tab pomodoro -->
      <div x-show="currentTab === 'pomodoro'" x-transition x-cloak>
        <x-timer></x-timer>
      </div>

      <!-- tab pausa breve -->
      <div x-show="currentTab === 'shortBreak'" x-transition x-cloak>
        <x-timer></x-timer>
      </div>

      <div x-show="currentTab === 'longBreak'" x-transition x-cloak>
        <x-timer></x-timer>
      </div>

    </div>

    <!-- pulsanti inizia Riprendi e pausa -->
    <div class="flex w-full justify-center mb-4">
      <button x-show="!running && !paused" @click="start()"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-full font-bold shadow-lg transition transform hover:scale-105">
        Inizia ⏵
      </button x-cloak>
      <button x-show="paused" @click="start()" x-cloak
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-full font-bold shadow-lg transition transform hover:scale-105">
        Riprendi ⏵
      </button>
      <button x-show="running" @click="pause()" x-cloak
        class="bg-amber-500 hover:bg-amber-600 text-white px-10 py-4 rounded-full font-bold shadow-lg transition transform hover:scale-105"
        x-cloak>
        Pausa ⏸
      </button>
    </div>

    <!-- pulsante reset -->
    <div class="flex justify-center gap-4">
      <div class="">
        <button x-cloak x-show="paused " @click="reset()"
          class="bg-red-600 hover:bg-red-700 text-white px-10 py-4 rounded-full font-bold shadow-lg transition transform hover:scale-105">
          Reset ↻
        </button>
      </div>

      <!-- pulsante completato pomodoro -->
      <div>
        <button x-cloak x-show="paused && !isBreak " @click="completed()"
          class="bg-green-600 hover:bg-green-700 text-white px-10 py-4 rounded-full font-bold shadow-lg transition transform hover:scale-105">
          Completa Pomodoro
        </button>
      </div>
    </div>


    <!-- descrizione -->
    <div x-show="currentTab === 'pomodoro'" class="text-gray-800 mb-8 mt-8 justify-center flex">
      <label>
        <span class="sr-only">Tipo di attività</span>
      </label>
      <input type="text" placeholder="Attività che stai svolgendo" x-model="description"
        class="block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>



    <!-- menu dropdown per selezionare la durata del pomodoro -->
    <div x-data="{ open: false }" class="relative inline-block pt-6 text-left flex justify-center">

      <button @click="open = !open" type="button"
        class="inline-flex justify-center gap-x-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        Opzioni Tempo
        <!-- freccia in basso -->
        <svg class="-mr-1 size-5 text-white" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd"
            d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
            clip-rule="evenodd" />
        </svg>
      </button>

      <div x-show="open" @click.away="open = false"
        class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-xl ring-1 ring-black/5 focus:outline-none"
        x-cloak>
        <div class="py-1 border-none">
          @forelse(TimerPreset::cases() as $preset)
            <button @click="setTime({{ $preset->value }}); open = false"
              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-500 hover:text-white">{{ $preset->value / 60 }}
              Minuti</button>
          @empty
          @endforelse
        </div>
      </div>
    </div>

    <div class="mt-10 pt-6 border-t w-full text-center">
      <p class="text-gray-500 text-sm">Sessioni ultimate: <span class="font-bold text-indigo-600">{{ $count }}</span>
      </p>
    </div>

    <!-- lista timer per utente -->
    <div class="mt-8 w-full max-w-md">
      <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Pomodori recenti</h3>

      <div class="space-y-3">
        <!-- per ogni pomodoro -->
        @forelse($logs as $log)
          <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center gap-3">
              <!-- icona pomodoro -->
              <div class="bg-indigo-100 p-2 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <!-- data pomodoro -->
              <div>
                <p class="text-sm font-medium text-gray-900">{{ $log->created_at->locale('it')->diffForHumans() }}</p>
              </div>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-400">Descrizione</p>
              <span class="text-indigo-600 font-bold">
                {{ $log->description }}
              </span>
            </div>
            <!-- durata pomodoro -->
            <div class="text-right">
              <p class="text-sm font-medium text-gray-400">Durata</p>
              <span class="text-indigo-600 font-bold">
                {{ (floor($log->duration / 60) == 0) ? '' : floor($log->duration / 60) . 'm' }}
                {{ $log->duration % 60 }}s
              </span>
            </div>
          </div>
        @empty
          <p class="text-gray-400 text-sm text-center py-4">Nessun log trovato. Inizia la tua prima sessione!</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
</div>