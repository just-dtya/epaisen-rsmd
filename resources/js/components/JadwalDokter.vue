<script setup>
import { ref, computed, onMounted } from 'vue'

const loading = ref(true)
const error = ref(null)
const rawData = ref(null)

// Format tanggal lokal (YYYY-MM-DD)
const getTodayLocal = () => {
  const d = new Date()
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const selectedDate = ref(getTodayLocal())
const activeDayId = ref(1)
const searchQuery = ref('')

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'https://192.168.1.190/api'

// Fetch data
const fetchJadwal = async () => {
  loading.value = true
  error.value = null
  try {
    const url = new URL(`${API_BASE_URL}/jadwal-dokter`)
    if (selectedDate.value) {
      url.searchParams.append('tanggal', selectedDate.value)
    }

    const res = await fetch(url.toString())
    if (!res.ok) throw new Error(`HTTP ${res.status}: Gagal memuat jadwal dokter`)
    const json = await res.json()
    
    if (!json.success) throw new Error(json.message || 'Respon API tidak valid')
    rawData.value = json
    
  } catch (err) {
    error.value = err.message || 'Koneksi ke endpoint gagal'
  } finally {
    loading.value = false
  }
}

// Handler perubahan Datepicker
const onDateChange = () => {
  const d = new Date(selectedDate.value + 'T00:00:00')
  const dayIndex = d.getDay() // 0: Minggu, 1: Senin, ..., 6: Sabtu
  activeDayId.value = dayIndex === 0 ? 7 : dayIndex
  fetchJadwal()
}

// Handler klik Tab Hari
const onDayTabClick = (targetIdHari) => {
  if (activeDayId.value === targetIdHari) return
  
  activeDayId.value = targetIdHari
  
  const current = new Date(selectedDate.value + 'T00:00:00')
  const currentDayIndex = current.getDay() === 0 ? 7 : current.getDay()
  
  const diffDays = targetIdHari - currentDayIndex
  current.setDate(current.getDate() + diffDays)
  
  const year = current.getFullYear()
  const month = String(current.getMonth() + 1).padStart(2, '0')
  const day = String(current.getDate()).padStart(2, '0')
  
  selectedDate.value = `${year}-${month}-${day}`
  fetchJadwal()
}

const daftarHari = computed(() => rawData.value?.data?.daftar_hari || [])
const metaInfo = computed(() => rawData.value?.meta || {})

// Filter list dokter
const jadwalFiltered = computed(() => {
  if (!rawData.value?.data?.jadwal_mingguan) return []
  
  const listHari = rawData.value.data.jadwal_mingguan[String(activeDayId.value)] || []
  
  if (!searchQuery.value.trim()) return listHari
  
  const q = searchQuery.value.toLowerCase()
  return listHari.filter(item => 
    item.nama_dokter.toLowerCase().includes(q) ||
    item.deskripsi_poli.toLowerCase().includes(q)
  )
})

const formatJam = (jam) => {
  if (!jam) return '-'
  return jam.substring(0, 5)
}

onMounted(async () => {
  const d = new Date(selectedDate.value + 'T00:00:00')
  const dayIndex = d.getDay()
  activeDayId.value = dayIndex === 0 ? 7 : dayIndex
  await fetchJadwal()
})
</script>

<template>
  <div class="space-y-3.5">
    
    <!-- 1. Header & Refresh Action -->
    <div class="flex items-center justify-between">
      <div>
        <div class="flex items-center gap-2">
          <h3 class="text-base font-bold text-base-content tracking-tight">Jadwal Praktik Dokter</h3>
          <span v-if="metaInfo.hari" class="badge badge-primary badge-xs py-2 px-2 font-medium">
            {{ metaInfo.hari }}
          </span>
        </div>
        <p class="text-xs text-base-content/60">Informasi jam praktik & status kehadiran</p>
      </div>

      <button 
        @click="fetchJadwal" 
        class="btn btn-sm btn-circle btn-ghost border border-base-300 bg-base-100 shadow-xs" 
        :class="{ 'animate-spin': loading }"
        aria-label="Refresh Data"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </div>

    <!-- 2. Date Picker Selector -->
    <div class="card bg-base-100 border border-base-300 shadow-sm p-3 rounded-xl">
      <div class="flex items-center justify-between gap-2">
        <label for="pilihTanggal" class="text-xs font-semibold text-base-content flex items-center gap-1.5 shrink-0">
          <span>📅</span> Tanggal Periksa:
        </label>
        <input 
          id="pilihTanggal"
          v-model="selectedDate"
          @change="onDateChange"
          type="date" 
          class="input input-bordered input-xs rounded-lg text-xs bg-base-200/60 font-mono"
        />
      </div>
    </div>

    <!-- 3. Tab Hari Interaktif -->
    <div class="flex gap-1.5 overflow-x-auto pb-1 no-scrollbar">
      <button
        v-for="hari in daftarHari"
        :key="hari.id_hari"
        @click="onDayTabClick(hari.id_hari)"
        class="btn btn-xs rounded-lg px-3 shrink-0 font-medium transition-all"
        :class="activeDayId === hari.id_hari ? 'btn-primary text-primary-content font-bold shadow-xs' : 'btn-ghost bg-base-100 border border-base-300 text-base-content/70'"
      >
        {{ hari.nama_hari }}
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div class="skeleton h-28 w-full rounded-2xl"></div>
      <div class="skeleton h-28 w-full rounded-2xl"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-error text-xs shadow-sm rounded-xl">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div class="flex-1">
        <p class="font-bold">Gagal memuat jadwal</p>
        <p class="opacity-80">{{ error }}</p>
      </div>
      <button @click="fetchJadwal" class="btn btn-xs btn-neutral">Coba Lagi</button>
    </div>

    <!-- Main Content List -->
    <div v-else class="space-y-3">
      
      <!-- Search Input -->
      <div class="relative">
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Cari poli atau dokter..." 
          class="input input-sm input-bordered w-full bg-base-100 pl-9 rounded-xl text-xs"
        />
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <button 
          v-if="searchQuery" 
          @click="searchQuery = ''" 
          class="btn btn-ghost btn-xs btn-circle absolute right-2 top-1"
        >
          ✕
        </button>
      </div>

      <!-- Empty State -->
      <div v-if="jadwalFiltered.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center text-xs text-base-content/60 rounded-xl">
        Tidak ada jadwal dokter untuk kategori/hari ini.
      </div>

      <!-- Doctor Cards -->
      <div v-else class="space-y-3">
        <div 
          v-for="(jadwal, idx) in jadwalFiltered" 
          :key="idx"
          class="card bg-base-100 rounded-2xl border border-base-300 shadow-sm p-4 space-y-3 transition-all"
          :class="jadwal.tgl_libur ? 'bg-base-200/50 border-dashed opacity-90' : ''"
        >
          <!-- Header: Nama Poli (Auto-wrap) & Status Praktik/Libur -->
          <div class="flex items-start justify-between gap-2.5">
            <!-- Poli Badge (Multi-line safe) -->
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary/10 text-primary border border-primary/20 text-xs font-bold leading-relaxed break-words flex-1">
              <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
              <span>{{ jadwal.deskripsi_poli }}</span>
            </div>

            <!-- Status Indicator -->
            <div class="shrink-0">
              <span 
                v-if="!jadwal.tgl_libur" 
                class="badge badge-success badge-sm text-[10px] font-bold text-success-content gap-1"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                Praktik
              </span>
              <span 
                v-else 
                class="badge badge-error badge-sm text-[10px] font-bold gap-1"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                Libur
              </span>
            </div>
          </div>

          <!-- Keterangan Libur / Cuti Alert (Jika Dokter Libur) -->
          <div 
            v-if="jadwal.tgl_libur" 
            class="p-2.5 rounded-xl bg-error/10 border border-error/20 text-error flex items-start gap-2 text-xs"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="leading-tight">
              <span class="font-bold block text-[11px] mb-0.5">Keterangan Tidak Praktik:</span>
              <span class="text-xs break-words">{{ jadwal.ket_libur || 'Izin / Cuti Praktik' }}</span>
            </div>
          </div>

          <!-- Doctor Name & Hours -->
          <div class="space-y-1.5 pt-0.5">
            <div class="font-bold text-sm text-base-content flex items-center gap-2 leading-snug">
              <div class="avatar placeholder shrink-0">
                <div class="bg-base-200 text-primary w-7 h-7 rounded-full text-xs flex items-center justify-center border border-base-300">
                  👨‍⚕️
                </div>
              </div>
              <span class="break-words flex-1">{{ jadwal.nama_dokter }}</span>
            </div>
            
            <div class="flex items-center gap-2 text-xs text-base-content/70 pl-9">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-base-content/50 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Jam Praktik: <strong class="text-base-content font-mono">{{ formatJam(jadwal.jam_mulai_praktik) }} - {{ formatJam(jadwal.jam_selesai_praktik) }}</strong> WIB</span>
            </div>
          </div>

          <!-- Footer Action Button -->
          <div class="pt-2.5 border-t border-base-200 flex items-center justify-between gap-2">
            <span class="text-[10px] text-base-content/60 font-medium">Layanan Pasien</span>
            
            <a 
              v-if="!jadwal.tgl_libur && jadwal.link_daftar_online"
              :href="jadwal.link_daftar_online" 
              target="_blank" 
              rel="noopener noreferrer"
              class="btn btn-xs btn-primary gap-1.5 rounded-lg shadow-xs"
            >
              <span>Daftar Online</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </a>
            <span v-else class="badge badge-ghost badge-sm text-[10px] font-semibold text-base-content/50">
              Pendaftaran Tidak Tersedia
            </span>
          </div>

        </div>
      </div>

    </div>

  </div>
</template>