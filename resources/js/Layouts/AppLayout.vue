<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AppNavbar from '@/components/AppNavbar.vue'
import AppDrawer from '@/components/AppDrawer.vue'
import BottomNav from '@/components/BottomNav.vue'
import PwaInstallPrompt from '@/components/PwaInstallPrompt.vue'
import SplashScreen from '@/components/SplashScreen.vue'

const page = usePage()
const currentUser = computed(() => page.props.patient || page.props.auth?.patient || page.props.auth?.user || null)

const isOnline = ref(navigator.onLine)
const isDrawerOpen = ref(false)

// Cek status sessionStorage di level inisialisasi script agar tidak ter-reset saat ganti page
const hasShownSession = typeof window !== 'undefined' && sessionStorage.getItem('rsmd_splash_shown') === 'true'
const showSplash = ref(!hasShownSession)

const handleSplashFinish = () => {
  showSplash.value = false
  sessionStorage.setItem('rsmd_splash_shown', 'true')
}

// === EDGE SWIPE GESTURE ===
const touchEdgeStartX = ref(0)
const touchEdgeStartY = ref(0)

const onGlobalTouchStart = (e) => {
  if (isDrawerOpen.value) return
  const x = e.touches[0].clientX
  if (x <= 35) {
    touchEdgeStartX.value = x
    touchEdgeStartY.value = e.touches[0].clientY
  } else {
    touchEdgeStartX.value = 0
  }
}

const onGlobalTouchEnd = (e) => {
  if (!touchEdgeStartX.value || isDrawerOpen.value) return
  const endX = e.changedTouches[0].clientX
  const endY = e.changedTouches[0].clientY
  const deltaX = endX - touchEdgeStartX.value
  const deltaY = Math.abs(endY - touchEdgeStartY.value)

  if (deltaX >= 60 && deltaY < 80) {
    isDrawerOpen.value = true
  }
  touchEdgeStartX.value = 0
}

const handleLogout = () => {
  router.post('/logout')
}

onMounted(() => {
  window.addEventListener('online', () => (isOnline.value = true))
  window.addEventListener('offline', () => (isOnline.value = false))

  const savedTheme = localStorage.getItem('rsmd_theme') || 'light'
  document.documentElement.setAttribute('data-theme', savedTheme)
})
</script>

<template>
  <!-- 1. Overlay SplashScreen sebagai modal/layer atas hanya saat showSplash aktif -->
  <SplashScreen v-if="showSplash" @finish="handleSplashFinish" />

  <!-- 2. Konten utama layout SELALU dirender agar tidak ada flicker DOM/re-mount layout -->
  <div
    class="min-h-screen bg-base-200 flex flex-col pb-20 select-none"
    @touchstart="onGlobalTouchStart"
    @touchend="onGlobalTouchEnd"
  >
    <AppNavbar
      :isOnline="isOnline"
      @toggle-drawer="isDrawerOpen = !isDrawerOpen"
    />

    <AppDrawer
      :show="isDrawerOpen"
      :user="currentUser"
      @close="isDrawerOpen = false"
      @logout="handleLogout"
    />

    <main class="flex-1 w-full">
      <slot />
    </main>

    <BottomNav />
    <PwaInstallPrompt />
  </div>
</template>
