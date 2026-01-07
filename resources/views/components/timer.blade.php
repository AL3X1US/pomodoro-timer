<div>
  <!-- timer -->
  <div class="text-8xl font-black text-indigo-600 mb-8 font-mono tracking-tighter">
    <span x-text="Math.floor(timeLeft / 60).toString().padStart(2, '0')"></span>:<span
      x-text="(timeLeft % 60).toString().padStart(2, '0')"></span>
  </div>
</div>