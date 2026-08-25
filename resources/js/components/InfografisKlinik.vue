<script setup>
import { ref, computed, onMounted } from 'vue'
import BaseModal from './BaseModal.vue'

const loading = ref(true)
const error = ref(null)
const rawData = ref({})
const searchQuery = ref('')
const selectedFilter = ref('all')

// State Modal
const showDemografiModal = ref(false)
const selectedKlinik = ref(null)

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'https://192.168.1.190/api'
const API_URL = `${API_BASE_URL}/infografis`

const fetchInfografis = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await fetch(API_URL)
    if (!res.ok) throw new Error(`HTTP ${res.status}: Gagal terhubung ke server`)
    const data = await res.json()
    rawData.value = data
  } catch (err) {
    error.value = err.message || 'Koneksi ke endpoint gagal'
  } finally {
    loading.value = false
  }
}

// Summary Metrics
const totalGender = computed(() => rawData.value?.total_jenis_kelamin || { L: 0, P: 0 })
const totalKunjungan = computed(() => rawData.value?.total_jenis_kunjungan || { LAMA: 0, BARU: 0 })
const totalUmur = computed(() => rawData.value?.total_golongan_umur || {})

const totalSemuaPasien = computed(() => {
  return (totalGender.value.L || 0) + (totalGender.value.P || 0)
})

const allKlinikList = computed(() => {
  if (!rawData.value) return []
  return Object.keys(rawData.value)
    .filter(key => !key.startsWith('total_'))
    .map(namaKlinik => ({
      nama: namaKlinik,
      ...rawData.value[namaKlinik]
    }))
})

const paymentMetrics = computed(() => {
  let bpjs = 0
  let total = 0
  allKlinikList.value.forEach(k => {
    k.dokter?.forEach(d => {
      d.jenis_pembayaran?.forEach(p => {
        total += p.total_pasien
        if (p.jenis_pembayaran === 'BPJS') bpjs += p.total_pasien
      })
    })
  })
  const persentaseBpjs = total > 0 ? Math.round((bpjs / total) * 100) : 0
  return { bpjs, total, persentaseBpjs }
})

const filteredKlinik = computed(() => {
  let list = [...allKlinikList.value]

  if (selectedFilter.value === 'top') {
    list.sort((a, b) => b.total_klinik - a.total_klinik)
  } else if (selectedFilter.value === 'active') {
    list = list.filter(k => k.total_klinik >= 3)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(k => {
      const matchKlinik = k.nama.toLowerCase().includes(q)
      const matchDokter = k.dokter?.some(d => d.nama_dokter.toLowerCase().includes(q))
      return matchKlinik || matchDokter
    })
  }

  return list
})

onMounted(() => {
  fetchInfografis()
})
</script>

<template>
  <div class="space-y-3.5">
    
    <!-- 1. Header -->
    <div class="flex items-center justify-between">
      <div>
        <div class="flex items-center gap-2">
          <h3 class="text-sm font-bold text-base-content tracking-tight">Infografis Pelayanan</h3>
          <span class="badge badge-primary badge-xs py-2 px-2 font-mono">Live</span>
        </div>
        <p class="text-xs text-base-content/60">Monitoring sebaran pasien rawat jalan</p>
      </div>

      <button 
        @click="fetchInfografis" 
        class="btn btn-sm btn-circle btn-ghost border border-base-300 bg-base-100 shadow-2xs" 
        :class="{ 'animate-spin': loading }"
        aria-label="Refresh Data"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div class="skeleton h-24 w-full rounded-2xl"></div>
      <div class="skeleton h-14 w-full rounded-xl"></div>
      <div class="skeleton h-14 w-full rounded-xl"></div>
    </div>

    <!-- Error Alert -->
    <div v-else-if="error" class="alert alert-error text-xs shadow-sm rounded-2xl">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div class="flex-1">
        <p class="font-bold">Gagal sinkronisasi data</p>
        <p class="opacity-80">{{ error }}</p>
      </div>
      <button @click="fetchInfografis" class="btn btn-xs btn-neutral">Coba Lagi</button>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-3.5">
      
      <!-- Summary Card -->
      <div class="card bg-base-100 rounded-2xl border border-base-300 shadow-sm p-4 space-y-3">
        <div class="grid grid-cols-2 gap-2">
          <div class="bg-base-200/50 rounded-xl p-3 border border-base-300/60">
            <span class="text-[10px] font-bold text-base-content/60 block uppercase tracking-wider">Total Pasien</span>
            <div class="flex items-baseline gap-1 mt-0.5">
              <span class="text-2xl font-black text-primary">{{ totalSemuaPasien }}</span>
              <span class="text-[10px] text-base-content/50">Orang</span>
            </div>
          </div>

          <div class="bg-base-200/50 rounded-xl p-3 border border-base-300/60">
            <span class="text-[10px] font-bold text-base-content/60 block uppercase tracking-wider">Pasien BPJS</span>
            <div class="flex items-baseline gap-1 mt-0.5">
              <span class="text-2xl font-black text-success">{{ paymentMetrics.persentaseBpjs }}%</span>
              <span class="text-[10px] text-base-content/50">({{ paymentMetrics.bpjs }})</span>
            </div>
          </div>
        </div>

        <button 
          @click="showDemografiModal = true"
          class="btn btn-sm btn-ghost w-full bg-base-200/40 border border-base-300/80 rounded-xl flex items-center justify-between text-xs font-semibold text-base-content hover:bg-base-200"
        >
          <div class="flex items-center gap-2">
            <span>👥</span>
            <span>Lihat Demografi Pasien RS (Usia & Gender)</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- Search & Filters -->
      <div class="space-y-2">
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
          <button v-if="searchQuery" @click="searchQuery = ''" class="btn btn-ghost btn-xs btn-circle absolute right-2 top-1">✕</button>
        </div>

        <div class="flex gap-1.5 overflow-x-auto pb-0.5 no-scrollbar text-xs">
          <button 
            @click="selectedFilter = 'all'"
            class="btn btn-xs rounded-lg px-3"
            :class="selectedFilter === 'all' ? 'btn-primary text-primary-content shadow-2xs' : 'btn-ghost bg-base-100 border border-base-300 text-base-content/70'"
          >
            Semua ({{ allKlinikList.length }})
          </button>
          <button 
            @click="selectedFilter = 'top'"
            class="btn btn-xs rounded-lg px-3"
            :class="selectedFilter === 'top' ? 'btn-primary text-primary-content shadow-2xs' : 'btn-ghost bg-base-100 border border-base-300 text-base-content/70'"
          >
            🔥 Terbanyak
          </button>
          <button 
            @click="selectedFilter = 'active'"
            class="btn btn-xs rounded-lg px-3"
            :class="selectedFilter === 'active' ? 'btn-primary text-primary-content shadow-2xs' : 'btn-ghost bg-base-100 border border-base-300 text-base-content/70'"
          >
            ⚡ Pasien ≥ 3
          </button>
        </div>
      </div>

      <!-- List Card Poli -->
      <div v-if="filteredKlinik.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center text-xs text-base-content/60 rounded-2xl">
        Poli atau dokter tidak ditemukan.
      </div>

      <div v-else class="space-y-2">
        <div 
          v-for="klinik in filteredKlinik" 
          :key="klinik.nama"
          @click="selectedKlinik = klinik"
          class="card bg-base-100 rounded-2xl border border-base-300 shadow-2xs p-3.5 space-y-2 hover:border-primary/50 cursor-pointer active:scale-[0.99] transition-all"
        >
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 truncate">
              <span class="w-2 h-2 rounded-full bg-primary shrink-0"></span>
              <span class="text-xs font-bold text-base-content truncate">{{ klinik.nama }}</span>
            </div>
            
            <div class="flex items-center gap-1.5 shrink-0">
              <span class="badge badge-primary badge-sm font-bold">
                {{ klinik.total_klinik }} Pasien
              </span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <progress 
              class="progress progress-primary w-full h-1.5" 
              :value="klinik.total_klinik" 
              :max="totalSemuaPasien || 1"
            ></progress>
            <span class="text-[10px] text-base-content/50 font-mono shrink-0">
              {{ totalSemuaPasien > 0 ? Math.round((klinik.total_klinik / totalSemuaPasien) * 100) : 0 }}%
            </span>
          </div>

          <div class="text-[11px] text-base-content/60 truncate">
            👨‍⚕️ {{ klinik.dokter?.map(d => d.nama_dokter).join(', ') }}
          </div>
        </div>
      </div>

    </div>

    <!-- MODAL 1: DEMOGRAFI PASIEN RS (Reusable BaseModal) -->
    <BaseModal 
      :show="showDemografiModal" 
      @close="showDemografiModal = false"
      title="Demografi Seluruh Pasien"
      subtitle="Distribusi usia, gender, dan jenis kunjungan"
      icon="👥"
    >
      <!-- Gender & Kunjungan -->
      <div class="grid grid-cols-2 gap-2">
        <div class="p-3 bg-base-200/50 rounded-xl border border-base-300/60 space-y-1">
          <span class="text-[10px] font-bold text-base-content/50 uppercase">Jenis Kelamin</span>
          <div class="flex items-center justify-between text-xs font-bold pt-0.5">
            <span class="text-info">L: {{ totalGender.L || 0 }}</span>
            <span class="text-secondary">P: {{ totalGender.P || 0 }}</span>
          </div>
        </div>

        <div class="p-3 bg-base-200/50 rounded-xl border border-base-300/60 space-y-1">
          <span class="text-[10px] font-bold text-base-content/50 uppercase">Tipe Kunjungan</span>
          <div class="flex items-center justify-between text-xs font-bold pt-0.5">
            <span class="text-success">Baru: {{ totalKunjungan.BARU || 0 }}</span>
            <span class="text-warning">Lama: {{ totalKunjungan.LAMA || 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Usia Pasien -->
      <div class="space-y-2 pt-1">
        <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/60 block">
          Sebaran Kelompok Usia
        </span>
        <div class="grid grid-cols-3 gap-1.5">
          <div 
            v-for="(count, range) in totalUmur" 
            :key="range" 
            class="p-2 bg-base-200/60 rounded-xl border border-base-300 text-center space-y-0.5"
          >
            <span class="text-[10px] text-base-content/60 block">{{ range }} th</span>
            <span class="text-xs font-black text-primary">{{ count }}</span>
          </div>
        </div>
      </div>
    </BaseModal>

    <!-- MODAL 2: DETAIL LENGKAP POLI (Reusable BaseModal) -->
    <BaseModal 
      :show="!!selectedKlinik" 
      @close="selectedKlinik = null"
      :title="selectedKlinik?.nama || ''"
      subtitle="Rincian beban antrean dan dokter praktik"
      icon="🏥"
    >
      <div v-if="selectedKlinik" class="space-y-3.5">
        <!-- Total Pasien Poli -->
        <div class="p-3 bg-base-200/50 rounded-2xl border border-base-300/60 flex items-center justify-between">
          <span class="text-xs font-semibold text-base-content/70">Total Pasien Poli Ini</span>
          <span class="badge badge-primary badge-md font-black">{{ selectedKlinik.total_klinik }} Pasien</span>
        </div>

        <!-- Dokter & Metode Penjamin -->
        <div class="space-y-2">
          <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/60 block">
            Dokter Praktek & Metode Penjamin
          </span>
          <div 
            v-for="(dok, dIdx) in selectedKlinik.dokter" 
            :key="dIdx" 
            class="bg-base-200/40 rounded-xl p-3 border border-base-300/60 space-y-2"
          >
            <div class="flex items-center gap-2 font-bold text-xs text-primary">
              <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-xs">🩺</span>
              <span class="break-words">{{ dok.nama_dokter }}</span>
            </div>

            <div class="flex flex-wrap gap-1.5 pl-8">
              <span 
                v-for="(byr, bIdx) in dok.jenis_pembayaran" 
                :key="bIdx"
                class="badge badge-xs font-semibold"
                :class="byr.jenis_pembayaran === 'BPJS' ? 'badge-success text-success-content' : 'badge-neutral text-neutral-content'"
              >
                {{ byr.jenis_pembayaran }}: {{ byr.total_pasien }} Pasien
              </span>
            </div>
          </div>
        </div>

        <!-- Demografi Khusus Poli -->
        <div class="grid grid-cols-2 gap-2">
          <div class="p-2.5 bg-base-200/40 rounded-xl border border-base-300/50">
            <span class="text-[10px] font-bold text-base-content/50 uppercase block mb-1">GENDER</span>
            <div class="flex gap-2 font-bold text-xs">
              <span class="text-info">L: {{ selectedKlinik.jenisKelamin?.L || 0 }}</span>
              <span class="text-secondary">P: {{ selectedKlinik.jenisKelamin?.P || 0 }}</span>
            </div>
          </div>

          <div class="p-2.5 bg-base-200/40 rounded-xl border border-base-300/50">
            <span class="text-[10px] font-bold text-base-content/50 uppercase block mb-1">STATUS PASIEN</span>
            <div class="flex gap-2 font-bold text-xs">
              <span class="text-success">Baru: {{ selectedKlinik.jenisKunjungan?.BARU || 0 }}</span>
              <span class="text-warning">Lama: {{ selectedKlinik.jenisKunjungan?.LAMA || 0 }}</span>
            </div>
          </div>
        </div>

        <!-- Sebaran Usia Poli -->
        <div v-if="selectedKlinik.golonganUmur" class="space-y-1.5">
          <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/60 block">Rentang Usia Pasien Poli</span>
          <div class="flex flex-wrap gap-1">
            <span 
              v-for="(count, range) in selectedKlinik.golonganUmur" 
              :key="range" 
              class="badge badge-ghost badge-xs font-mono"
            >
              {{ range }} th ({{ count }})
            </span>
          </div>
        </div>
      </div>
    </BaseModal>

  </div>
</template>