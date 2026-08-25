<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  beds: {
    type: Array,
    default: () => []
  },
  summary: {
    type: Object,
    default: () => ({ total: 0, kosong: 0, terisi: 0 })
  },
  lastUpdated: {
    type: String,
    default: ''
  }
})

const isRefreshing = ref(false)
const searchQuery = ref('')

const handleRefresh = () => {
  isRefreshing.value = true
  router.reload({
    only: ['beds', 'summary', 'lastUpdated'],
    onFinish: () => {
      isRefreshing.value = false
    }
  })
}

// Occupancy Rate (BOR / Bed Occupancy Rate)
const occupancyRate = computed(() => {
  if (!props.summary.total || props.summary.total === 0) return 0
  return Math.round((props.summary.terisi / props.summary.total) * 100)
})

// Filter data kelas
const filteredBeds = computed(() => {
  if (!searchQuery.value) return props.beds
  const q = searchQuery.value.toLowerCase().trim()
  return props.beds.filter(b => b.NamaKelas?.toLowerCase().includes(q))
})

// Visual dot color per class
const getClassAccent = (name) => {
  const n = (name || '').toUpperCase()
  if (n.includes('VIP')) return 'bg-amber-500 text-amber-500'
  if (n.includes('KELAS 1')) return 'bg-sky-500 text-sky-500'
  if (n.includes('KELAS 2')) return 'bg-teal-500 text-teal-500'
  if (n.includes('KELAS 3')) return 'bg-emerald-500 text-emerald-500'
  if (n.includes('ISOLASI')) return 'bg-rose-500 text-rose-500'
  return 'bg-primary text-primary'
}
</script>

<template>
  <Head title="Monitoring Tempat Tidur Ranap" />

  <AppLayout>
    <main class="p-4 space-y-3.5 max-w-md mx-auto w-full select-none pb-24">

      <!-- 1. Header Minimalis -->
      <div class="flex items-center justify-between px-1 pt-1">
        <div>
          <h1 class="text-base font-black tracking-tight text-base-content flex items-center gap-2">
            Ketersediaan Bed
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
              Live SIMRS
            </span>
          </h1>
          <p class="text-[11px] text-base-content/50 mt-0.5">
            RS Mata Daerah Soepardjo Roestam
          </p>
        </div>

        <button
          @click="handleRefresh"
          :disabled="isRefreshing"
          class="btn btn-sm btn-circle btn-ghost bg-base-100 border border-base-300 shadow-2xs text-base-content/60 hover:text-primary transition-all active:scale-95"
          title="Segarkan Data"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            :class="{ 'animate-spin text-primary': isRefreshing }"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>

      <!-- 2. Ringkasan Kartu Dashboard Minimalis -->
      <div class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
        <!-- 3 Metrik Angka -->
        <div class="grid grid-cols-3 divide-x divide-base-200">
          <div class="text-center px-1">
            <span class="text-[10px] font-bold text-base-content/50 uppercase tracking-wider block">Total</span>
            <span class="text-xl font-black font-mono text-base-content tracking-tight">{{ summary.total }}</span>
            <span class="text-[9px] text-base-content/40 block">Kapasitas</span>
          </div>

          <div class="text-center px-1">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Kosong</span>
            <span class="text-xl font-black font-mono text-emerald-600 tracking-tight">{{ summary.kosong }}</span>
            <span class="text-[9px] text-emerald-600/70 font-semibold block">Siap Pakai</span>
          </div>

          <div class="text-center px-1">
            <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider block">Terisi</span>
            <span class="text-xl font-black font-mono text-rose-500 tracking-tight">{{ summary.terisi }}</span>
            <span class="text-[9px] text-rose-500/70 font-semibold block">Pasien Ranap</span>
          </div>
        </div>

        <!-- Bar Rasio Keterisian Global -->
        <div class="pt-2 border-t border-base-200/80 space-y-1.5">
          <div class="flex justify-between items-center text-[10px] font-semibold text-base-content/60">
            <span>Tingkat Keterisian (BOR)</span>
            <span class="font-mono font-bold" :class="occupancyRate > 80 ? 'text-rose-500' : 'text-primary'">
              {{ occupancyRate }}% Terpakai
            </span>
          </div>
          <div class="w-full bg-base-200 h-2 rounded-full overflow-hidden flex">
            <div
              class="bg-rose-500 h-full transition-all duration-500"
              :style="{ width: `${occupancyRate}%` }"
            ></div>
            <div
              class="bg-emerald-500 h-full transition-all duration-500"
              :style="{ width: `${100 - occupancyRate}%` }"
            ></div>
          </div>
        </div>

        <!-- Footer Info -->
        <div class="flex items-center justify-between text-[10px] text-base-content/40 pt-1">
          <span class="flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Pembaruan: {{ lastUpdated }}
          </span>
          <span>Perawatan Rawat Inap</span>
        </div>
      </div>

      <!-- 3. Quick Search Filter -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Filter nama ruang / kelas..."
          class="input input-sm w-full bg-base-100 rounded-2xl pl-9 pr-8 text-xs focus:outline-primary border-base-300 shadow-2xs"
        />
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <button
          v-if="searchQuery"
          @click="searchQuery = ''"
          class="btn btn-xs btn-circle btn-ghost absolute right-1.5 top-1.5 text-base-content/40"
        >
          ✕
        </button>
      </div>

      <!-- 4. Empty State -->
      <div v-if="filteredBeds.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center rounded-3xl space-y-2">
        <p class="text-2xl">🛏️</p>
        <h3 class="text-xs font-bold text-base-content">Kelas tidak ditemukan</h3>
        <p class="text-[11px] text-base-content/50">Coba gunakan kata kunci pencarian kelas lain.</p>
      </div>

      <!-- 5. List Kelas Kamar (Modern Clean Card) -->
      <div v-else class="space-y-2.5">
        <div
          v-for="(item, idx) in filteredBeds"
          :key="idx"
          class="card bg-base-100 border border-base-300 shadow-2xs hover:border-primary/40 rounded-3xl p-3.5 space-y-2.5 transition-all"
        >
          <!-- Baris Atas: Nama Kelas + Status Badge -->
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <span
                class="w-2.5 h-2.5 rounded-full"
                :class="getClassAccent(item.NamaKelas).split(' ')[0]"
              ></span>
              <h2 class="text-xs font-extrabold text-base-content tracking-tight">
                {{ item.NamaKelas }}
              </h2>
            </div>

            <span
              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
              :class="item.jumlah_kosong > 0 ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-600'"
            >
              <span class="w-1.5 h-1.5 rounded-full" :class="item.jumlah_kosong > 0 ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></span>
              {{ item.jumlah_kosong > 0 ? `${item.jumlah_kosong} Bed Tersedia` : 'Penuh' }}
            </span>
          </div>

          <!-- Baris Bawah: Status Metrik Ringkas -->
          <div class="flex items-center justify-between bg-base-200/50 rounded-2xl px-3 py-2 border border-base-300/50 text-[11px]">
            <span class="text-base-content/60 font-medium">
              Kapasitas: <strong class="text-base-content font-mono">{{ item.total }}</strong>
            </span>

            <div class="flex items-center gap-3 font-mono font-bold">
              <span class="text-emerald-600">
                {{ item.jumlah_kosong }} <span class="text-[9px] font-normal text-base-content/50">Kosong</span>
              </span>
              <span class="text-base-content/20">•</span>
              <span class="text-rose-500">
                {{ item.jumlah_terisi }} <span class="text-[9px] font-normal text-base-content/50">Terisi</span>
              </span>
            </div>
          </div>
        </div>
      </div>

    </main>
  </AppLayout>
</template>
