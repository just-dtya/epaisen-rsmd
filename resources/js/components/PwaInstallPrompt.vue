<script setup>
import { ref, onMounted } from 'vue'

const deferredPrompt = ref(null)
const showInstallBanner = ref(false)

onMounted(() => {
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferredPrompt.value = e
    showInstallBanner.value = true
  })

  window.addEventListener('appinstalled', () => {
    showInstallBanner.value = false
    deferredPrompt.value = null
  })
})

const installPwa = async () => {
  if (!deferredPrompt.value) return
  deferredPrompt.value.prompt()
  const { outcome } = await deferredPrompt.value.userChoice
  if (outcome === 'accepted') {
    showInstallBanner.value = false
  }
  deferredPrompt.value = null
}
</script>

<template>
  <Transition name="slide-up">
    <div 
      v-if="showInstallBanner" 
      class="fixed bottom-20 left-4 right-4 z-40 max-w-md mx-auto"
    >
      <div class="card bg-base-100 shadow-xl border border-primary/20 p-3.5 rounded-2xl flex flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-base-200 p-1 border border-base-300 shrink-0">
            <img src="/icon_rsmd.png" alt="Logo" class="w-full h-full object-contain" />
          </div>
          <div class="min-w-0">
            <p class="text-xs font-bold text-base-content leading-tight truncate">Pasang RSMD Mobile</p>
            <p class="text-[10px] text-base-content/60 leading-tight">Akses cepat langsung dari layar HP</p>
          </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
          <button @click="showInstallBanner = false" class="btn btn-ghost btn-xs btn-circle">✕</button>
          <button @click="installPwa" class="btn btn-xs btn-primary font-bold rounded-lg shadow-xs">
            Install
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease-out;
}
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>