<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  hasilRadiologi: {
    type: [Array, Object],
    default: () => []
  }
})

// Normalisasi array Radiologi
const radItems = computed(() => {
  if (!props.hasilRadiologi) return []
  if (Array.isArray(props.hasilRadiologi)) return props.hasilRadiologi
  if (typeof props.hasilRadiologi === 'object') {
    return props.hasilRadiologi.hasilRadiologi || [props.hasilRadiologi]
  }
  return []
})

// State Lightbox Image Radiologi
const activeImage = ref(null)
const openModal = (imgSrc) => { activeImage.value = imgSrc }
const closeModal = () => { activeImage.value = null }
</script>

<template>
  <div v-if="radItems.length > 0" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
    <!-- Header Radiologi -->
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-7 h-7 rounded-xl bg-blue-500/10 text-blue-500 font-bold text-sm flex items-center justify-center">
        🩻
      </div>
      <div>
        <h3 class="text-xs font-black text-base-content">Hasil Radiologi & Ekspertise</h3>
        <p class="text-[9px] text-base-content/50">Hasil pemeriksaan rontgen / pencitraan</p>
      </div>
    </div>

    <!-- Items Radiologi -->
    <div class="space-y-3 text-xs">
      <div
        v-for="(rad, idx) in radItems"
        :key="idx"
        class="bg-base-200/40 p-3 rounded-2xl border border-base-200 space-y-2.5"
      >
        <!-- Nama Item & Dokter -->
        <div class="flex items-start justify-between gap-2">
          <div>
            <span class="font-bold text-primary text-[11px] block">
              {{ rad.nmItem || rad.pemeriksaan || 'Foto Radiologi' }}
            </span>
            <span v-if="rad.nmDokterInterpretasi || rad.dokter" class="text-[9px] text-base-content/50 block">
              Dokter Radiologi: {{ rad.nmDokterInterpretasi || rad.dokter }}
            </span>
            <span v-if="rad.nmRadiografer" class="text-[9px] text-base-content/40 block">
              Radiografer: {{ rad.nmRadiografer }}
            </span>
          </div>
          <span v-if="rad.kdTrs" class="text-[9px] badge badge-ghost badge-sm font-mono shrink-0">
            {{ rad.kdTrs }}
          </span>
        </div>

        <!-- Kesan / Hasil Radiologi -->
        <div class="bg-base-100/80 p-2.5 rounded-xl border border-base-200/80">
          <span class="text-[9px] font-bold text-base-content/50 uppercase block mb-1">Kesan / Interpretasi:</span>
          <p class="text-[10px] font-medium text-base-content leading-relaxed whitespace-pre-line">
            {{ rad.kesan || rad.hasil || '-' }}
          </p>
        </div>

        <!-- Thumbnail Foto Rontgen Base64 -->
        <div v-if="rad.file && rad.file.length > 0" class="space-y-1 pt-1">
          <span class="text-[9px] font-bold text-base-content/50 block">Foto Radiologi ({{ rad.file.length }} Gambar):</span>
          <div class="flex gap-2 overflow-x-auto pb-1">
            <div
              v-for="(imgObj, imgIdx) in rad.file"
              :key="imgIdx"
              @click="openModal(imgObj.file)"
              class="relative group cursor-pointer border border-base-300 rounded-xl overflow-hidden shrink-0 w-16 h-16 bg-black/5"
            >
              <img :src="imgObj.file" alt="Foto Radiologi" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
              <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition-opacity">
                Lihat
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div v-if="activeImage" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4" @click.self="closeModal">
      <div class="relative max-w-3xl w-full bg-base-100 rounded-3xl overflow-hidden shadow-2xl border border-base-300">
        <div class="flex items-center justify-between p-3 border-b border-base-200 bg-base-100">
          <span class="text-xs font-bold">Preview Foto Radiologi</span>
          <button @click="closeModal" class="btn btn-xs btn-circle btn-ghost">✕</button>
        </div>
        <div class="p-2 bg-black flex items-center justify-center max-h-[80vh] overflow-auto">
          <img :src="activeImage" alt="Radiologi Full" class="max-w-full max-h-[75vh] object-contain rounded-lg" />
        </div>
      </div>
    </div>
  </div>
</template>
