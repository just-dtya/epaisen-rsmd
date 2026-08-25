<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  camera: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['play-camera'])

const antrianPoli = ref(null)
const loadingAntrian = ref(false)

const ANTRIAN_POLI_API = import.meta.env.VITE_API_ANTRIAN_POLI_URL || 'https://api.rsmdsr.id/api/antrian'

const sisaAntrean = computed(() => {
  if (!antrianPoli.value) return 0
  const total = antrianPoli.value.total_antrian || 0
  const terlayani = antrianPoli.value.jumlah_terlayani || 0
  return Math.max(0, total - terlayani)
})

const badgeKepadatan = computed(() => {
  const status = antrianPoli.value?.status?.toLowerCase()
  if (status === 'sepi') return 'badge-success'
  if (status === 'sedang') return 'badge-warning'
  if (status === 'ramai') return 'badge-error'
  return 'badge-ghost'
})

const fetchAntrianPoli = async () => {
  if (!props.camera.kode) return
  loadingAntrian.value = true
  try {
    const res = await fetch(`${ANTRIAN_POLI_API}?klinik=${encodeURIComponent(props.camera.kode)}`)
    const json = await res.json()
    if (json.Status === '000' && json.Data) {
      antrianPoli.value = json.Data
    }
  } catch (err) {
    console.error(`Gagal fetch antrean ${props.camera.kode}:`, err)
  } finally {
    loadingAntrian.value = false
  }
}

onMounted(() => {
  fetchAntrianPoli()
})
</script>

<template>
  <div class="card bg-base-100 border border-base-300 rounded-2xl p-3.5 shadow-2xs space-y-3 hover:border-primary/40 transition-all">
    
    <!-- Baris Judul & Status CCTV -->
    <div class="flex items-start justify-between gap-2">
      <div class="min-w-0 flex-1 space-y-0.5">
        <h4 class="text-xs font-bold text-base-content leading-snug break-words">
          {{ camera.name }}
        </h4>
        <div class="flex items-center gap-2 text-[10px] text-base-content/60">
          <span class="font-mono font-semibold">{{ camera.kode }}</span>
          <span>•</span>
          <span>🕒 {{ antrianPoli?.jam_praktik || `${camera.jam_buka} - ${camera.jam_tutup}` }}</span>
        </div>
      </div>

      <!-- Tombol Live CCTV / Badge No CCTV -->
      <button 
        v-if="camera.url" 
        @click="$emit('play-camera', camera)"
        class="btn btn-xs btn-primary font-bold rounded-lg gap-1 shrink-0 shadow-2xs active:scale-95"
      >
        <span>📹 Putar</span>
      </button>
      <span v-else class="badge badge-ghost border border-base-300 text-[9px] font-medium text-base-content/40 shrink-0">
        No CCTV
      </span>
    </div>

    <!-- Antrean Metrics Box -->
    <div class="p-2.5 bg-base-200/50 rounded-xl border border-base-300/40 space-y-2">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-1.5">
          <span class="text-[10px] font-bold text-base-content/70 uppercase tracking-wider">Antrean</span>
          <span v-if="antrianPoli?.status" class="badge badge-xs uppercase font-bold text-[9px]" :class="badgeKepadatan">
            {{ antrianPoli.status }}
          </span>
        </div>

        <span class="text-[10px] font-mono text-base-content/50">
          Update: {{ antrianPoli?.data_per ? antrianPoli.data_per.split(' ')[1] : '-' }}
        </span>
      </div>

      <div class="grid grid-cols-3 gap-2 text-center">
        <!-- Antrean Terakhir / Dipanggil -->
        <div class="p-1.5 bg-base-100 rounded-lg border border-base-300/40 shadow-2xs">
          <span class="text-[9px] text-base-content/50 block">Dipanggil</span>
          <span class="text-xs font-black text-primary font-mono leading-tight">
            {{ antrianPoli?.antrian_terakhir || '-' }}
          </span>
        </div>

        <!-- Terlayani -->
        <div class="p-1.5 bg-base-100 rounded-lg border border-base-300/40 shadow-2xs">
          <span class="text-[9px] text-success block">Terlayani</span>
          <span class="text-xs font-black text-success font-mono leading-tight">
            {{ antrianPoli?.jumlah_terlayani ?? 0 }}
          </span>
        </div>

        <!-- Total Pasien -->
        <div class="p-1.5 bg-base-100 rounded-lg border border-base-300/40 shadow-2xs">
          <span class="text-[9px] text-base-content/50 block">Total</span>
          <span class="text-xs font-black text-base-content font-mono leading-tight">
            {{ antrianPoli?.total_antrian ?? 0 }}
          </span>
        </div>
      </div>
    </div>

  </div>
</template>