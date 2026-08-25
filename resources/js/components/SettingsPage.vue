<script setup>
import { ref, onMounted } from 'vue'

defineProps({
  user: {
    type: Object,
    default: () => ({
      username: 'admin',
      nama_user: 'administrator.',
      role: 'admin'
    })
  }
})

defineEmits(['logout'])

// Daftar tema DaisyUI dengan preview warna
const themes = [
  { name: 'light', label: 'Terang', icon: '☀️', previewBg: 'bg-[#ffffff]', previewPrimary: 'bg-[#00a884]' },
  { name: 'dark', label: 'Gelap', icon: '🌙', previewBg: 'bg-[#1d232a]', previewPrimary: 'bg-[#10b981]' }
]

const currentTheme = ref('light')

const applyTheme = (themeName) => {
  currentTheme.value = themeName
  document.documentElement.setAttribute('data-theme', themeName)
  localStorage.setItem('rsmd_theme', themeName)
}

onMounted(() => {
  const savedTheme = localStorage.getItem('rsmd_theme') || 'light'
  currentTheme.value = savedTheme
  document.documentElement.setAttribute('data-theme', savedTheme)
})
</script>

<template>
  <main class="p-4 space-y-4 max-w-md mx-auto w-full">
    
    <!-- 1. Header Card Profil User -->
    <div class="card bg-base-100 border border-base-300 shadow-sm p-4 rounded-3xl flex flex-row items-center gap-3.5">
      <div class="avatar placeholder shrink-0">
        <div class="bg-primary/10 text-primary border border-primary/20 w-12 h-12 rounded-2xl font-black flex items-center justify-center text-sm shadow-2xs">
          {{ user?.username ? user.username.substring(0, 2).toUpperCase() : 'RS' }}
        </div>
      </div>
      
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-1.5">
          <h2 class="text-xs font-bold text-base-content truncate">
            {{ user?.nama_user || user?.username || 'Petugas Medis' }}
          </h2>
          <span class="badge badge-primary/15 text-primary border-0 text-[9px] font-bold px-1.5 py-0 h-4 uppercase shrink-0">
            {{ user?.role || 'Staff' }}
          </span>
        </div>
        <p class="text-[11px] text-base-content/50 font-mono mt-0.5 truncate">
          ID: {{ user?.user_id || '1695003175lj435' }}
        </p>
      </div>
    </div>

    <!-- 2. Pilihan Tema DaisyUI -->
    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-3xl p-4 space-y-3">
      <div>
        <h3 class="text-xs font-bold text-base-content uppercase tracking-wider">Tampilan & Tema</h3>
        <p class="text-[11px] text-base-content/60">Pilih skema warna antarmuka yang nyaman.</p>
      </div>

      <div class="grid grid-cols-1 gap-2">
        <button
          v-for="t in themes"
          :key="t.name"
          @click="applyTheme(t.name)"
          type="button"
          class="flex items-center justify-between p-3 bg-base-200/40 rounded-2xl border transition-all active:scale-[0.99]"
          :class="currentTheme === t.name ? 'border-primary ring-2 ring-primary/20 bg-base-200/80 shadow-2xs' : 'border-base-300/60 hover:bg-base-200/60'"
        >
          <!-- Kiri: Icon & Nama Tema -->
          <div class="flex items-center gap-2.5">
            <span class="text-lg">{{ t.icon }}</span>
            <div class="text-left">
              <p class="text-xs font-bold text-base-content">{{ t.label }}</p>
              <p class="text-[10px] text-base-content/50 uppercase font-mono">{{ t.name }}</p>
            </div>
          </div>

          <!-- Kanan: Preview Warna & Checklist -->
          <div class="flex items-center gap-2">
            <div class="flex gap-1 p-1 rounded-lg border border-base-300 bg-base-100">
              <span class="w-3.5 h-3.5 rounded-full border border-black/10 shadow-2xs" :class="t.previewBg"></span>
              <span class="w-3.5 h-3.5 rounded-full border border-black/10 shadow-2xs" :class="t.previewPrimary"></span>
            </div>

            <!-- Radio Indicator -->
            <div 
              v-if="currentTheme === t.name" 
              class="w-5 h-5 rounded-full bg-primary text-primary-content flex items-center justify-center shadow-xs"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <div v-else class="w-5 h-5 rounded-full border border-base-300"></div>
          </div>
        </button>
      </div>
    </div>

    <!-- 3. Tombol Logout Akun -->
    <div class="pt-1">
      <button 
        @click="$emit('logout')" 
        type="button"
        class="btn btn-error btn-outline btn-block rounded-2xl text-xs font-bold gap-2 shadow-xs"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span>Keluar dari Akun</span>
      </button>
    </div>

  </main>
</template>