<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import InfografisKlinik from './InfografisKlinik.vue'

const props = defineProps({
  user: {
    type: Object,
    default: null
  }
})

const page = usePage()

// Ambil user dari props parent atau langsung dari Inertia Shared State (HandleInertiaRequests)
const currentUser = computed(() => {
  return props.user || page.props.auth?.patient || page.props.auth?.user || null
})

// Nama lengkap user
const fullName = computed(() => {
  if (!currentUser.value) return 'Pasien / Pengunjung'
  return currentUser.value.nama || currentUser.value.nm_pasien || currentUser.value.nama_user || currentUser.value.name || 'Pasien'
})

// Salam otomatis berdasarkan waktu
const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour >= 4 && hour < 11) return 'Selamat Pagi'
  if (hour >= 11 && hour < 15) return 'Selamat Siang'
  if (hour >= 15 && hour < 18) return 'Selamat Sore'
  return 'Selamat Malam'
})

// Tanggal live lokal Indonesia
const formattedDate = computed(() => {
  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  }).format(new Date())
})

// Menu dengan rute URL Laravel Inertia
const quickMenus = [
  {
    id: 'daftar',
    label: 'Pendaftaran',
    href: '/pendaftaran',
    bg: 'bg-primary/10 text-primary',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  },
  {
    id: 'antrean',
    label: 'Antrean Poli',
    href: '/antrean',
    bg: 'bg-primary/10 text-primary',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    id: 'jadwal',
    label: 'Jadwal Dokter',
    href: '/jadwal-dokter',
    bg: 'bg-primary/10 text-primary',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
  },
  {
    id: 'farmasi',
    label: 'Farmasi & Obat',
    href: '/farmasi',
    bg: 'bg-primary/10 text-primary',
    icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'
  },
  {
    id: 'liat_rsmd',
    label: 'LiatRSMD',
    href: '/rsmd',
    bg: 'bg-primary/10 text-primary',
    icon: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'
  }
]
</script>

<template>
  <main class="p-4 space-y-4 max-w-md mx-auto w-full">

    <!-- 1. Header Card Pasien -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-primary/10 via-base-100 to-base-100 border border-base-300 shadow-sm p-4.5">

      <div class="absolute -right-8 -bottom-8 w-36 h-36 rounded-full bg-primary/5 blur-xl pointer-events-none"></div>
      <div class="absolute -left-8 -top-8 w-28 h-28 rounded-full bg-secondary/5 blur-lg pointer-events-none"></div>

      <div class="relative z-10 space-y-3">

        <!-- Row 1: Date Chip & Logo -->
        <div class="flex items-center justify-between">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-base-100/90 border border-base-300/80 text-[11px] font-semibold text-base-content/70 shadow-2xs">
            <span class="w-1.5 h-1.5 rounded-full bg-success animate-pulse"></span>
            <span>{{ formattedDate }}</span>
          </div>

          <div class="relative shrink-0">
            <div class="w-9 h-9 rounded-xl bg-base-100 p-1.5 border border-base-300 shadow-2xs flex items-center justify-center">
              <img src="/icon_rsmd.png" alt="Logo RSMD" class="w-full h-full object-contain" />
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 flex h-2.5 w-2.5 items-center justify-center rounded-full bg-base-100 border border-base-300 shadow-xs">
              <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
            </span>
          </div>
        </div>

        <!-- Row 2: User Info -->
        <div class="space-y-1 pt-0.5">
          <p class="text-xs font-semibold text-base-content/60 flex items-center gap-1">
            <span>{{ greeting }},</span>
            <span>👋</span>
          </p>

          <h2 class="text-base font-extrabold text-base-content tracking-tight leading-snug break-words">
            {{ fullName }}
          </h2>

          <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
            <span class="badge badge-primary/15 text-primary border-0 text-[10px] font-bold px-2 py-0 h-4.5 uppercase shrink-0">
              {{ currentUser?.no_rkm_medis ? 'Pasien' : 'Umum' }}
            </span>
            <span class="text-[10px] text-base-content/30">•</span>
            <span class="text-[11px] text-base-content/50 font-mono truncate">
              No. RM: {{ currentUser?.no_rkm_medis || '-' }}
            </span>
          </div>
        </div>

        <!-- Row 3: Bottom System Info Banner -->
        <div class="pt-2.5 border-t border-base-300/60 flex items-center justify-between text-[11px]">
          <div class="flex items-center gap-1.5 text-base-content/70">
            <div class="w-4 h-4 rounded-md bg-primary/10 text-primary flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <span class="font-medium">SIMRS Terintegrasi</span>
          </div>

          <span class="badge badge-sm badge-ghost border border-base-300 text-[10px] font-semibold text-primary">
            ePasien
          </span>
        </div>

      </div>
    </div>

    <!-- 2. Quick Menus (Inertia Link) -->
    <div class="grid grid-cols-4 gap-2">
      <Link
        v-for="(menu, idx) in quickMenus"
        :key="idx"
        :href="menu.href"
        class="flex flex-col items-center justify-center p-3 bg-base-100 rounded-2xl border border-base-300 shadow-sm active:scale-95 transition-all duration-150 group"
      >
        <div
          class="w-10 h-10 rounded-xl flex items-center justify-center mb-1.5 transition-transform group-hover:scale-105"
          :class="menu.bg"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menu.icon" />
          </svg>
        </div>
        <span class="text-[11px] font-semibold text-center text-base-content truncate w-full">{{ menu.label }}</span>
      </Link>
    </div>

    <!-- 3. Infografis Pelayanan Komponen -->
    <InfografisKlinik />

  </main>
</template>
