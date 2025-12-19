<div class="flex flex-col items-center justify-center p-8 shadow-xl bg-white rounded-3xl border border-gray-100" x-data="{ 
    timeLeft: 1500, 
    timer: null,
    running: false,
    paused: false,
    timeSelected: 1500,

    init() {
      this.showLogs();
    },
  
    start() {
      this.running = true;
      this.paused = false;
      this.timer = setInterval(() => {
        if (this.timeLeft > 0) this.timeLeft--;
          else this.finish();
      }, 1000);
    },
  
    pause() {
      this.running = false;
      this.paused = true;
      clearInterval(this.timer);
    },
  
    finish() {
      this.pause();
      // Qui 'parliamo' con la classe PHP del Backend
      let tempoTrascorso = this.timeSelected - this.timeLeft;
      $wire.incrementPomodoro(tempoTrascorso, 'Pomodoro');

      alert('Ottimo lavoro! Sessione completata');
      this.timeSelected = 1500;
      this.timeLeft = 1500;
      this.paused = false;
      this.running = false;
    },

    setTime(time) {
      this.timeSelected = time;
      this.timeLeft = time;
    },

    showLogs() {
      $wire.showLogs();
    }
  }">

  <h2 class="text-gray-400 font-bold uppercase tracking-widest text-sm mb-6">Timer Pomodoro</h2>

  <!-- timer -->
  <div class="text-8xl font-black text-indigo-600 mb-8 font-mono tracking-tighter">
    <span x-text="Math.floor(timeLeft / 60).toString().padStart(2, '0')"></span>:<span
      x-text="(timeLeft % 60).toString().padStart(2, '0')"></span>
  </div>

  <!-- pulsanti pausa e play -->
  <div class="flex gap-4">
    <div>
      <button x-show="!running && !paused" @click="start()"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-full font-bold shadow-lg transition transform hover:scale-105">
        Inizia ⏵
      </button>
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
    <div>
      <button @click="finish()"
        class="bg-red-600 hover:bg-red-700 text-white px-10 py-4 rounded-full font-bold shadow-lg transition transform hover:scale-105">
        Reset ↻
      </button>
    </div>
  </div>

  <!-- menu dropdown per selezionare la durata del pomodoro -->
  <div x-data="{ open: false }" class="relative inline-block pt-6 text-left">

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
        <button @click="setTime(300); open = false"
          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-500 hover:text-white">5
          Minuti</button>
        <button @click="setTime(1500); open = false"
          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-500 hover:text-white">25
          Minuti</button>
      </div>
    </div>
  </div>

  <div class="mt-10 pt-6 border-t w-full text-center">
    <p class="text-gray-500 text-sm">Sessioni ultimate: <span
        class="font-bold text-indigo-600">{{ $count }}</span></p>
  </div>

  <!-- lista timer per utente -->
  <div class="mt-8 w-full max-w-md">
    <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Recenti Focus Logs</h3>

    <div class="space-y-3">
      @forelse($logs as $log)
        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
          <div class="flex items-center gap-3">
            <div class="bg-indigo-100 p-2 rounded-lg">
              <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">{{ $log->created_at->diffForHumans() }}</p>
              <p class="text-xs text-gray-500">Durata sessione</p>
            </div>
          </div>
          <div class="text-right">
            <span class="text-indigo-600 font-bold">
              {{ floor($log->duration / 60) }}m {{ $log->duration % 60 }}s
            </span>
          </div>
        </div>
      @empty
        <p class="text-gray-400 text-sm text-center py-4">Nessun log trovato. Inizia la tua prima sessione!</p>
      @endforelse
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>