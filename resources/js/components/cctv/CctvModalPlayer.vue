<script setup>
import { ref, computed, watch, nextTick, onUnmounted } from 'vue'
import Hls from 'hls.js'
import BaseModal from '../BaseModal.vue'

const props = defineProps({
  camera: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close'])

const ANTRIAN_POLI_API = import.meta.env.VITE_API_ANTRIAN_POLI_URL || 'https://api.rsmdsr.id/api/antrian'

const videoPlayer = ref(null)
const isPlaying = ref(false)
const hasError = ref(false)
const errorMessage = ref('')
const antrianData = ref(null)
const loadingAntrian = ref(false)
let hlsInstance = null

// Nama lengkap klinik dari respons API antrian
const namaKlinikLengkap = computed(() => {
  return antrianData.value?.klinik || props.camera?.name || 'Poliklinik RSMD'
})

// Murni mengambil jam_praktik dari respons API
const jamPraktik = computed(() => {
  return antrianData.value?.jam_praktik || '-'
})

// Sisa antrean tunggu
const sisaAntrean = computed(() => {
  if (!antrianData.value) return 0
  const total = antrianData.value.total_antrian || 0
  const terlayani = antrianData.value.jumlah_terlayani || 0
  return Math.max(0, total - terlayani)
})

// Warna badge status kepadatan
const badgeStatus = computed(() => {
  const status = antrianData.value?.status?.toLowerCase()
  if (status === 'sepi') return 'badge-success'
  if (status === 'sedang') return 'badge-warning'
  if (status === 'ramai') return 'badge-error'
  return 'badge-ghost'
})

// Fetch live data antrean
const fetchAntrianModal = async (kode) => {
  if (!kode) return
  loadingAntrian.value = true
  try {
    const res = await fetch(`${ANTRIAN_POLI_API}?klinik=${encodeURIComponent(kode)}`)
    const json = await res.json()
    if (json.Status === '000' && json.Data) {
      antrianData.value = json.Data
    }
  } catch (err) {
    console.error('Gagal memuat antrean di modal:', err)
  } finally {
    loadingAntrian.value = false
  }
}

const playStream = (streamUrl) => {
  if (!videoPlayer.value || !streamUrl) return

  destroyHls()
  hasError.value = false
  errorMessage.value = ''

  if (Hls.isSupported()) {
    hlsInstance = new Hls({
      enableWorker: true,
      lowLatencyMode: true,
      backBufferLength: 30
    })

    hlsInstance.loadSource(streamUrl)
    hlsInstance.attachMedia(videoPlayer.value)

    hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
      videoPlayer.value.play().then(() => {
        isPlaying.value = true
      }).catch((e) => {
        console.warn('Autoplay dicegah:', e)
      })
    })

    hlsInstance.on(Hls.Events.ERROR, (event, data) => {
      if (data.fatal) {
        hasError.value = true
        errorMessage.value = 'Stream CCTV sedang tidak dapat diakses.'
        destroyHls()
      }
    })
  } else if (videoPlayer.value.canPlayType('application/vnd.apple.mpegurl')) {
    videoPlayer.value.src = streamUrl
    videoPlayer.value.addEventListener('loadedmetadata', () => {
      videoPlayer.value.play().then(() => {
        isPlaying.value = true
      }).catch(() => {})
    })
    videoPlayer.value.addEventListener('error', () => {
      hasError.value = true
      errorMessage.value = 'Stream tidak dapat diputar pada peramban ini.'
    })
  }
}

const destroyHls = () => {
  if (hlsInstance) {
    hlsInstance.destroy()
    hlsInstance = null
  }
  isPlaying.value = false
}

const handleClose = () => {
  destroyHls()
  antrianData.value = null
  emit('close')
}

watch(() => props.camera, (newCam) => {
  if (newCam) {
    fetchAntrianModal(newCam.kode)
    nextTick(() => {
      playStream(newCam.url)
    })
  } else {
    destroyHls()
    antrianData.value = null
  }
})

onUnmounted(() => {
  destroyHls()
})
</script>

<template>
  <BaseModal 
    :show="!!camera" 
    @close="handleClose"
    :title="namaKlinikLengkap"
    :subtitle="`🕒 ${jamPraktik}`"
    icon="📹"
  >
    <div v-if="camera" class="space-y-3.5">
      
      <!-- 1. Video Player Container -->
      <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-black border border-base-300 shadow-md flex items-center justify-center">
        
        <video 
          ref="videoPlayer" 
          class="w-full h-full object-contain"
          controls 
          playsinline 
          autoplay 
          muted
        ></video>

        <!-- Live Top Pill -->
        <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-black/75 backdrop-blur-xs text-[10px] font-bold text-white pointer-events-none z-10">
          <span class="w-2 h-2 rounded-full bg-error animate-ping"></span>
          LIVE CCTV
        </div>

        <div class="absolute top-2.5 right-2.5 z-10 pointer-events-none">
          <span class="badge badge-xs font-mono font-bold bg-base-100/90 text-base-content border-0">
            {{ camera.kode }}
          </span>
        </div>

        <!-- Error State Overlay -->
        <div v-if="hasError" class="absolute inset-0 bg-neutral-900/95 flex flex-col items-center justify-center p-4 text-center z-20 space-y-2">
          <div class="w-10 h-10 rounded-full bg-error/15 text-error flex items-center justify-center text-lg">⚠️</div>
          <p class="text-xs font-bold text-white">Gagal Memutar Stream</p>
          <p class="text-[10px] text-white/60 max-w-xs">{{ errorMessage }}</p>
          <button @click="playStream(camera.url)" class="btn btn-xs btn-primary rounded-lg font-bold">
            Coba Lagi
          </button>
        </div>
      </div>

      <!-- 2. Informasi Klinik & Jam Praktik -->
      <div class="p-3 bg-base-200/50 rounded-2xl border border-base-300/60 space-y-1">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-bold text-base-content/50 uppercase tracking-wider">Poliklinik</span>
          <span class="badge badge-primary/15 text-primary border-0 text-[10px] font-bold px-2 py-0 h-4.5 font-mono">
            {{ jamPraktik }}
          </span>
        </div>
        <p class="text-xs font-extrabold text-base-content leading-snug">
          {{ namaKlinikLengkap }}
        </p>
      </div>

      <!-- 3. Live Antrean Metrics Panel -->
      <div class="p-3.5 bg-gradient-to-b from-base-100 to-base-200/40 rounded-2xl border border-base-300 shadow-2xs space-y-2.5">
        
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-1.5">
            <span class="text-xs font-bold text-base-content">Status Antrean Ruang</span>
            <span v-if="antrianData?.status" class="badge badge-xs uppercase font-bold text-[9px]" :class="badgeStatus">
              {{ antrianData.status }}
            </span>
          </div>
          <span class="text-[10px] font-mono text-base-content/50">
            Update: {{ antrianData?.data_per ? antrianData.data_per.split(' ')[1] : '-' }}
          </span>
        </div>

        <!-- 3-Column Metrics Grid -->
        <div class="grid grid-cols-3 gap-2 text-center">
          
          <!-- Nomor Antrean Terakhir / Dipanggil -->
          <div class="p-2 bg-base-100 rounded-xl border border-base-300/60 shadow-2xs">
            <span class="text-[9px] font-bold uppercase text-base-content/50 block">Dipanggil</span>
            <span class="text-sm font-black text-primary font-mono leading-tight">
              {{ antrianData?.antrian_terakhir || '-' }}
            </span>
          </div>

          <!-- Terlayani -->
          <div class="p-2 bg-base-100 rounded-xl border border-base-300/60 shadow-2xs">
            <span class="text-[9px] font-bold uppercase text-success block">Terlayani</span>
            <span class="text-sm font-black text-success font-mono leading-tight">
              {{ antrianData?.jumlah_terlayani ?? 0 }}
            </span>
          </div>

          <!-- Total Antrean -->
          <div class="p-2 bg-base-100 rounded-xl border border-base-300/60 shadow-2xs">
            <span class="text-[9px] font-bold uppercase text-base-content/50 block">Total Pasien</span>
            <span class="text-sm font-black text-base-content font-mono leading-tight">
              {{ antrianData?.total_antrian ?? 0 }}
            </span>
          </div>

        </div>

        <!-- Sisa Pasien Menunggu Notice -->
        <div class="pt-1.5 border-t border-base-300/40 flex items-center justify-between text-[11px] text-base-content/60">
          <span>Sisa Pasien Belum Dilayani:</span>
          <span class="font-bold font-mono text-base-content">{{ sisaAntrean }} Pasien</span>
        </div>

      </div>

    </div>
  </BaseModal>
</template>