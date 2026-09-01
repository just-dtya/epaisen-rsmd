<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  idPendaftaran: {
    type: [String, Number],
    required: true
  },
  hasilRadiologi: {
    type: [Array, Object],
    default: () => []
  },
  error: {
    type: String,
    default: null
  }
})

// Normalisasi data radiologi ke bentuk Array
const radiologiItems = computed(() => {
  if (!props.hasilRadiologi) return []
  if (Array.isArray(props.hasilRadiologi)) return props.hasilRadiologi
  if (typeof props.hasilRadiologi === 'object') {
    return Object.keys(props.hasilRadiologi).length > 0 ? [props.hasilRadiologi] : []
  }
  return []
})

// State Lightbox Image Radiologi
const activeImage = ref(null)
const openModal = (imgSrc) => { activeImage.value = imgSrc }
const closeModal = () => { activeImage.value = null }
</script>

<template>
  <div class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center gap-3 border-b border-base-200 pb-3">
      <div class="w-9 h-9 rounded-2xl bg-blue-500/10 text-blue-600 font-bold text-base flex items-center justify-center shrink-0">
        🩻
      </div>
      <div>
        <h3 class="text-sm font-black text-base-content">Hasil Radiologi</h3>
        <p class="text-[10px] text-base-content/50">Hasil ekspertise foto & bacaan dokter radiologi</p>
      </div>
    </div>

    <!-- State: Error / Data Kosong -->
    <div v-if="error || radiologiItems.length === 0" class="py-8 text-center space-y-1">
      <div class="text-2xl">🖼️</div>
      <p class="text-xs font-bold text-base-content/70">
        {{ error || 'Belum ada data pemeriksaan radiologi' }}
      </p>
      <p class="text-[10px] text-base-content/40">Data radiologi untuk pendaftaran ini tidak tersedia.</p>
    </div>

    <!-- State: Ada Data -->
    <div v-else class="space-y-4">
      <div
        v-for="(rad, idx) in radiologiItems"
        :key="idx"
        class="bg-base-200/40 p-3.5 rounded-2xl border border-base-200 space-y-3"
      >
        <!-- Info Pemeriksaan & Dokter -->
        <div class="flex items-start justify-between gap-2">
          <div>
            <span class="font-bold text-primary text-xs block">
              {{ rad.nmItem || rad.pemeriksaan || 'Foto Radiologi' }}
            </span>
            <span v-if="rad.nmDokterInterpretasi || rad.dokter" class="text-[10px] text-base-content/50 block mt-0.5">
              Dokter Radiologi: {{ rad.nmDokterInterpretasi || rad.dokter }}
            </span>
          </div>
          <span v-if="rad.kdTrs" class="text-[9px] badge badge-ghost badge-sm font-mono shrink-0">
            {{ rad.kdTrs }}
          </span>
        </div>

        <!-- Kesan / Hasil Pembacaan -->
        <div class="bg-base-100/80 p-3 rounded-xl border border-base-200/70">
          <span class="text-[9px] font-bold text-base-content/50 uppercase block mb-1 tracking-wider">
            Kesan / Interpretasi:
          </span>
          <p class="text-xs font-medium text-base-content leading-relaxed whitespace-pre-line">
            {{ rad.kesan || rad.hasil || '-' }}
          </p>
        </div>

        <!-- Attachment Foto Radiologi -->
        <div v-if="rad.file && rad.file.length > 0" class="space-y-1">
          <span class="text-[9px] font-bold text-base-content/50 uppercase block tracking-wider">Lampiran Foto:</span>
          <div class="flex gap-2 overflow-x-auto pt-1 pb-1">
            <div
              v-for="(imgObj, imgIdx) in rad.file"
              :key="imgIdx"
              @click="openModal(imgObj.file || imgObj)"
              class="relative group cursor-pointer border border-base-300 rounded-xl overflow-hidden shrink-0 w-16 h-16 bg-black/5"
            >
              <img
                :src="imgObj.file || imgObj"
                alt="Foto Radiologi"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform"
              />
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition-opacity">
                Lihat
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal Foto Radiologi -->
    <div
      v-if="activeImage"
      class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="closeModal"
    >
      <div class="relative max-w-3xl w-full bg-base-100 rounded-3xl overflow-hidden shadow-2xl border border-base-300">
        <div class="flex items-center justify-between p-3 border-b border-base-200 bg-base-100">
          <span class="text-xs font-bold text-base-content">Preview Foto Radiologi</span>
          <button @click="closeModal" class="btn btn-xs btn-circle btn-ghost">✕</button>
        </div>
        <div class="p-2 bg-black flex items-center justify-center max-h-[80vh] overflow-auto">
          <img :src="activeImage" alt="Radiologi Full" class="max-w-full max-h-[75vh] object-contain rounded-lg" />
        </div>
      </div>
    </div>
  </div>
</template>
