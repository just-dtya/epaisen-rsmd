<script setup>
defineProps({
  camera: {
    type: Object,
    required: true
  }
})

defineEmits(['select'])
</script>

<template>
  <div 
    @click="$emit('select', camera)"
    class="card bg-base-100 border border-base-300 shadow-2xs rounded-2xl overflow-hidden cursor-pointer hover:border-primary/50 active:scale-[0.99] transition-all group"
  >
    <!-- Screen Backdrop / Player Preview -->
    <div class="relative h-44 w-full bg-neutral-900 flex items-center justify-center overflow-hidden">
      <!-- Glow Decor -->
      <div class="absolute inset-0 bg-radial from-primary/15 via-transparent to-transparent opacity-40 group-hover:opacity-70 transition-opacity"></div>
      
      <!-- Scanline Visual Overlay -->
      <div class="absolute inset-0 bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%)] bg-[length:100%_4px] pointer-events-none opacity-30"></div>

      <!-- Top Badges -->
      <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-black/75 backdrop-blur-xs text-[9px] font-bold text-white uppercase tracking-wider">
        <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
        LIVE HLS
      </div>

      <div class="absolute top-2.5 right-2.5">
        <span class="badge badge-xs font-mono font-bold bg-base-100/90 text-base-content border-0 shadow-xs">
          {{ camera.kode }}
        </span>
      </div>

      <!-- Play Button Glow -->
      <div class="relative z-10 w-13 h-13 rounded-2xl bg-primary text-primary-content flex items-center justify-center shadow-lg shadow-primary/30 group-hover:scale-110 transition-transform duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current ml-0.5" viewBox="0 0 24 24">
          <path d="M8 5v14l11-7z"/>
        </svg>
      </div>

      <!-- Bottom Status Strip -->
      <div class="absolute bottom-2 left-2.5 right-2.5 flex items-center justify-between text-[10px] text-white/80 font-mono pointer-events-none">
        <span class="truncate">{{ camera.url.split('/').pop() }}</span>
        <span class="text-success font-bold flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-success"></span> HD
        </span>
      </div>
    </div>

    <!-- Metadata Caption -->
    <div class="p-3.5 flex items-center justify-between gap-2">
      <div class="min-w-0 flex-1">
        <h4 class="text-xs font-bold text-base-content leading-snug truncate">{{ camera.name }}</h4>
        <p class="text-[11px] text-base-content/55 truncate mt-0.5">{{ camera.lokasi }}</p>
      </div>
      <span class="badge badge-success/15 text-success border-0 text-[10px] font-bold px-2 py-0 h-5 shrink-0">
        Aktif
      </span>
    </div>
  </div>
</template>