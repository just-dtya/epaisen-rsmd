<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Hls from 'hls.js'
import PoliStatusCard from '@/components/cctv/PoliStatusCard.vue'
import CctvModalPlayer from '@/components/cctv/CctvModalPlayer.vue'

// Endpoint Environment
const CAMERAS_API = import.meta.env.VITE_API_CAMERAS_URL || 'https://web.rsmdsr.id/api/cameras'
const ANTREAN_PENDAFTARAN_API = import.meta.env.VITE_API_ANTRIAN_PENDAFTARAN_URL || 'https://api.rsmdsr.id/api/antrian/pendaftaran'

const isRefreshing = ref(false)
const videoPlayer = ref(null)
const isStreamLoading = ref(true)
const searchQuery = ref('')
const selectedKlinikFilter = ref('ALL')
const selectedModalCam = ref(null)

let hlsInstance = null
let pollInterval = null

// Live Pendaftaran Stream Langsung (Direct Origin)
const pendaftaranCam = ref({
  id: 'pendaftaran',
  name: 'Lobi Utama & Loket Pendaftaran',
  url: import.meta.env.VITE_CCTV_STREAM_PENDAFTARAN || 'https://cctv.rsmdsr.id/cam4/stream.m3u8',
  kode: 'CAM-04'
})

// State Data API
const antreanPendaftaran = ref({
  jumlah_terlayani: 0,
  total_antrian: 0,
  data_per: '-'
})
const cameraList = ref([])
const loadingCameras = ref(false)

// Helper: Ambil nama klinik murni
const extractKlinikName = (fullName) => {
  if (!fullName) return ''
  return fullName.split(' - ')[0].trim()
}

// List Nama Poliklinik Unik untuk Dropdown
const klinikOptions = computed(() => {
  const map = new Set()
  cameraList.value.forEach(c => {
    const klinikName = extractKlinikName(c.name)
    if (klinikName) map.add(klinikName)
  })
  return Array.from(map).sort((a, b) => a.localeCompare(b))
})

// Sisa Antrean Pendaftaran
const sisaPendaftaran = computed(() => {
  const total = antreanPendaftaran.value.total_antrian || 0
  const terlayani = antreanPendaftaran.value.jumlah_terlayani || 0
  return Math.max(0, total - terlayani)
})

const progressPendaftaran = computed(() => {
  const total = antreanPendaftaran.value.total_antrian || 0
  if (total === 0) return 100
  return Math.round(((antreanPendaftaran.value.jumlah_terlayani || 0) / total) * 100)
})

// Filter Poliklinik
const filteredCameras = computed(() => {
  let list = cameraList.value

  if (selectedKlinikFilter.value !== 'ALL') {
    list = list.filter(c => extractKlinikName(c.name) === selectedKlinikFilter.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(c =>
      c.name.toLowerCase().includes(q) ||
      c.kode.toLowerCase().includes(q)
    )
  }

  return list
})

// 1. Fetch Antrean Pendaftaran
const fetchAntreanPendaftaran = async () => {
  try {
    const res = await fetch(ANTREAN_PENDAFTARAN_API)
    const json = await res.json()
    if (json.Status === '000' && json.Data) {
      antreanPendaftaran.value = json.Data
    }
  } catch (err) {
    console.error('Gagal fetch antrean pendaftaran:', err)
  }
}

// 2. Fetch List Cameras Poliklinik (Langsung simpan URL asli)
const fetchCameras = async () => {
  loadingCameras.value = true
  try {
    const res = await fetch(CAMERAS_API)
    const data = await res.json()
    if (Array.isArray(data)) {
      cameraList.value = data.map(cam => ({
        ...cam,
        url: cam.url || cam.stream_url
      }))
    }
  } catch (err) {
    console.error('Gagal fetch daftar kamera:', err)
  } finally {
    loadingCameras.value = false
  }
}

// 3. Inisialisasi Player HLS Pendaftaran (Direct Stream)
const initPendaftaranStream = (targetUrl) => {
  if (!videoPlayer.value || !targetUrl) return
  destroyHls()
  isStreamLoading.value = true

  if (Hls.isSupported()) {
    hlsInstance = new Hls({
      enableWorker: true,
      lowLatencyMode: true,
      backBufferLength: 30
    })
    hlsInstance.loadSource(targetUrl)
    hlsInstance.attachMedia(videoPlayer.value)
    hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
      isStreamLoading.value = false
      videoPlayer.value.play().catch(() => {})
    })
    hlsInstance.on(Hls.Events.ERROR, () => {
      isStreamLoading.value = false
    })
  } else if (videoPlayer.value.canPlayType('application/vnd.apple.mpegurl')) {
    videoPlayer.value.src = targetUrl
    videoPlayer.value.addEventListener('loadedmetadata', () => {
      isStreamLoading.value = false
      videoPlayer.value.play().catch(() => {})
    })
  }
}

const destroyHls = () => {
  if (hlsInstance) {
    hlsInstance.destroy()
    hlsInstance = null
  }
}

const refreshAll = async () => {
  isRefreshing.value = true
  await Promise.all([fetchAntreanPendaftaran(), fetchCameras()])
  initPendaftaranStream(pendaftaranCam.value.url)
  setTimeout(() => {
    isRefreshing.value = false
  }, 600)
}

onMounted(() => {
  nextTick(() => {
    initPendaftaranStream(pendaftaranCam.value.url)
  })
  fetchAntreanPendaftaran()
  fetchCameras()

  pollInterval = setInterval(fetchAntreanPendaftaran, 20000)
})

onUnmounted(() => {
  destroyHls()
  if (pollInterval) clearInterval(pollInterval)
})
</script>

<template>
  <Head title="LiatRSMD - CCTV & Antrean" />

  <AppLayout>
    <main class="p-4 space-y-4 max-w-md mx-auto w-full select-none">

      <!-- 1. Header LiatRSMD -->
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-base font-black text-base-content tracking-tight">LiatRSMD</h2>
            <span class="badge badge-error badge-xs py-2 px-2 text-white font-bold flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
              LIVE
            </span>
          </div>
          <p class="text-xs text-base-content/60">Surveillance CCTV & Antrean Poliklinik Terpadu</p>
        </div>

        <button
          @click="refreshAll"
          class="btn btn-sm btn-circle btn-ghost border border-base-300 bg-base-100 shadow-2xs"
          :class="{ 'animate-spin': isRefreshing || loadingCameras }"
          aria-label="Refresh Data"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>

      <!-- 2. Integrated CCTV Pendaftaran Card -->
      <div class="card bg-base-100 border border-base-300 shadow-xs rounded-3xl overflow-hidden space-y-0">

        <!-- Video Player Stream -->
        <div class="relative w-full aspect-video bg-neutral-950 flex items-center justify-center overflow-hidden">
          <video
            ref="videoPlayer"
            class="w-full h-full object-cover"
            playsinline
            autoplay
            muted
          ></video>

          <div v-if="isStreamLoading" class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center space-y-2 z-10">
            <span class="loading loading-spinner text-primary loading-sm"></span>
            <span class="text-[10px] text-white/80 font-mono">Menghubungkan Stream...</span>
          </div>

          <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-black/75 backdrop-blur-xs text-[9px] font-bold text-white uppercase tracking-wider z-10">
            <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
            LIVE PENDAFTARAN
          </div>

          <div class="absolute bottom-2 left-2.5 right-2.5 flex items-center justify-between text-[10px] text-white/90 font-medium pointer-events-none drop-shadow-md z-10">
            <span class="truncate">{{ pendaftaranCam.name }}</span>
          </div>
        </div>

        <!-- Live Antrean Pendaftaran Details -->
        <div class="p-4 bg-gradient-to-b from-base-100 to-base-200/40 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                🎫
              </div>
              <div>
                <h3 class="text-xs font-bold text-base-content leading-tight">Antrean Pendaftaran</h3>
                <p class="text-[10px] text-base-content/50 font-mono">Update: {{ antreanPendaftaran.data_per }}</p>
              </div>
            </div>

            <span class="badge badge-primary/15 text-primary border-0 text-[10px] font-bold px-2 py-0 h-5">
              Loket 1-4
            </span>
          </div>

          <div class="grid grid-cols-3 gap-2">
            <div class="p-2.5 bg-base-100 rounded-2xl border border-base-300/60 shadow-2xs text-center">
              <span class="text-[9px] font-bold uppercase text-base-content/50 block">Total</span>
              <span class="text-base font-black text-base-content font-mono">{{ antreanPendaftaran.total_antrian }}</span>
            </div>

            <div class="p-2.5 bg-base-100 rounded-2xl border border-base-300/60 shadow-2xs text-center">
              <span class="text-[9px] font-bold uppercase text-success block">Terlayani</span>
              <span class="text-base font-black text-success font-mono">{{ antreanPendaftaran.jumlah_terlayani }}</span>
            </div>

            <div class="p-2.5 bg-base-100 rounded-2xl border border-base-300/60 shadow-2xs text-center">
              <span class="text-[9px] font-bold uppercase text-primary block">Sisa Tunggu</span>
              <span class="text-base font-black text-primary font-mono">{{ sisaPendaftaran }}</span>
            </div>
          </div>

          <div class="space-y-1">
            <div class="flex justify-between text-[10px] font-semibold text-base-content/60">
              <span>Penyelesaian Loket</span>
              <span class="font-bold text-primary">{{ progressPendaftaran }}%</span>
            </div>
            <progress
              class="progress progress-primary w-full h-1.5"
              :value="antreanPendaftaran.jumlah_terlayani"
              :max="antreanPendaftaran.total_antrian || 1"
            ></progress>
          </div>
        </div>

      </div>

      <!-- 3. Toolbar Poliklinik -->
      <div class="space-y-2.5 pt-1">
        <div class="flex items-center justify-between px-1">
          <span class="text-xs font-bold uppercase tracking-wider text-base-content/60 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-primary"></span>
            Poliklinik Rawat Jalan ({{ filteredCameras.length }})
          </span>
          <span class="text-[10px] text-base-content/40 font-medium">Real-time SIMRS</span>
        </div>

        <!-- Dropdown Filter -->
        <div class="relative">
          <select
            v-model="selectedKlinikFilter"
            class="select select-sm select-bordered w-full bg-base-100 rounded-xl text-xs font-semibold focus:select-primary"
          >
            <option value="ALL">🏥 Semua Poliklinik</option>
            <option
              v-for="klinik in klinikOptions"
              :key="klinik"
              :value="klinik"
            >
              {{ klinik }}
            </option>
          </select>
        </div>

        <!-- Search Input -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari poli / kata kunci dokter..."
            class="input input-sm input-bordered w-full bg-base-100 pl-9 rounded-xl text-xs font-medium"
          />
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="btn btn-ghost btn-xs btn-circle absolute right-2 top-1 text-base-content/50"
          >
            ✕
          </button>
        </div>

        <!-- List Poliklinik Card -->
        <div v-if="loadingCameras" class="space-y-2">
          <div class="skeleton h-24 w-full rounded-2xl"></div>
          <div class="skeleton h-24 w-full rounded-2xl"></div>
        </div>

        <div v-else-if="filteredCameras.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center text-xs text-base-content/60 rounded-2xl">
          Poliklinik tidak ditemukan.
        </div>

        <div v-else class="space-y-2.5">
          <PoliStatusCard
            v-for="cam in filteredCameras"
            :key="cam.id"
            :camera="cam"
            @play-camera="selectedModalCam = $event"
          />
        </div>
      </div>

      <!-- 4. Modal Popup CCTV Poliklinik -->
      <CctvModalPlayer
        :camera="selectedModalCam"
        @close="selectedModalCam = null"
      />

    </main>
  </AppLayout>
</template>
