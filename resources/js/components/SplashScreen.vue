<script setup>
import { ref, onMounted } from 'vue'

const emit = defineEmits(['finish'])

// Cek langsung sebelum render template: apakah sesi ini sudah pernah menampilkan splash?
const hasShown = typeof window !== 'undefined' && sessionStorage.getItem('rsmd_splash_shown') === 'true'
const showSplash = ref(!hasShown)

onMounted(() => {
  if (showSplash.value) {
    // Jalankan timer 1.8 detik
    setTimeout(() => {
      showSplash.value = false
      sessionStorage.setItem('rsmd_splash_shown', 'true')
      emit('finish')
    }, 1800)
  } else {
    emit('finish')
  }
})
</script>

<template>
  <Transition name="splash-fade">
    <div
      v-if="showSplash"
      class="fixed inset-0 z-[9999] bg-base-200 flex flex-col items-center justify-between p-8 select-none overflow-hidden"
    >

      <!-- Ambient Light Glow Effects -->
      <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-primary/15 blur-3xl animate-pulse pointer-events-none"></div>
      <div class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full bg-secondary/15 blur-3xl animate-pulse pointer-events-none"></div>

      <!-- Spacer Atas -->
      <div class="h-6"></div>

      <!-- Center Content: Logo, Maskot, & Branding Animation -->
      <div class="flex flex-col items-center text-center space-y-5 relative z-10 animate-fade-in-up">

        <!-- Combined Logo & Mascot Floating Frame -->
        <div class="relative">
          <div class="w-24 h-24 rounded-3xl bg-base-100 p-3 border-2 border-base-300 shadow-xl flex items-center justify-center ring-4 ring-primary/10">
            <img
              src="/icon_rsmd.png"
              alt="Logo RSMD"
              class="w-full h-full object-contain"
            />
          </div>
        </div>

        <!-- App Title & Tagline -->
        <div class="space-y-1">
          <div class="flex items-center justify-center gap-1.5">
            <h1 class="text-2xl font-black text-base-content tracking-tight">
              RSMD<span class="text-primary font-bold">Mobile</span>
            </h1>
            <span class="badge badge-primary/10 text-primary border-0 text-[10px] font-bold px-2 py-0.5 h-5 rounded-md">
              v1.0
            </span>
          </div>
          <p class="text-xs text-base-content/60 font-medium">Sistem Layanan Digital Terpadu</p>
        </div>

        <!-- Subtle Loading Progress -->
        <div class="pt-4 flex flex-col items-center gap-2">
          <span class="loading loading-dots loading-sm text-primary"></span>
          <span class="text-[11px] text-base-content/40 font-mono tracking-wider">Menyiapkan Layanan...</span>
        </div>

      </div>

      <!-- Footer RS Identity -->
      <div class="text-center relative z-10">
        <p class="text-xs font-bold text-base-content/70">RSUD Prof. Dr. Soepardjo Roestam</p>
        <p class="text-[10px] text-base-content/40 font-mono mt-0.5">Solusi Terpercaya Kesehatan Anda</p>
      </div>

    </div>
  </Transition>
</template>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(18px) scale(0.96);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Transisi Halus Menghilang */
.splash-fade-enter-active,
.splash-fade-leave-active {
  transition: opacity 0.35s ease, transform 0.35s ease;
}

.splash-fade-enter-from,
.splash-fade-leave-to {
  opacity: 0;
  transform: scale(1.02);
}
</style>
