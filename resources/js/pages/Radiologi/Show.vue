<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  id_pendaftaran: {
    type: String,
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

// Normalisasi data radiologi agar selalu berbentuk Array
const radiologiItems = computed(() => {
  if (!props.hasilRadiologi) return []
  if (Array.isArray(props.hasilRadiologi)) return props.hasilRadiologi
  if (typeof props.hasilRadiologi === 'object') {
    return Object.keys(props.hasilRadiologi).length > 0 ? [props.hasilRadiologi] : []
  }
  return []
})

// Lightbox modal foto radiologi
const activeImage = ref(null)
const openModal = (imgSrc) => { activeImage.value = imgSrc }
const closeModal = () => { activeImage.value = null }
</script>

<template>
  <Head title="Hasil Radiologi" />

  <AppLayout>
    <main class="p-4 sm:p-5 max-w-md mx-auto w-full space-y-4 pb-12">
      <!-- Top Navigation Header -->
      <div class="flex items-center justify-between bg-base-100 p-3.5 rounded-3xl border border-base-300 shadow-xs">
        <div class="flex items-center gap-3">
          <Link href="/rekam-medis/radiologi" class="btn btn-sm btn-circle btn-ghost bg-base-200/60 hover:bg-base-200">
            ←
          </Link>
          <div>
            <h2 class="text-sm font-black text-base-content tracking-tight">Hasil Radiologi</h2>
            <p class="text-[10px] font-mono text-base-content/50">ID: {{ id_pendaftaran }}</p>
          </div>
        </div>
        <span class="px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-600 text-[10px] font-bold border border-blue-500/20">
          🩻 RADIOLOGI
        </span>
      </div>

      <!-- State: Error / Data Kosong -->
      <div v-if="error || radiologiItems.length === 0" class="card bg-base-100 border border-base-300 p-10 text-center rounded-3xl space-y-3 shadow-xs">
        <div class="w-14 h-14 bg-blue-500/10 text-blue-600 rounded-2xl flex items-center justify-center mx-auto text-2xl font-bold">
          🩻
        </div>
        <div class="space-y-1">
          <p class="text-xs font-bold text-base-content/80">{{ error || 'Data Radiologi Kosong' }}</p>
          <p class="text-[11px] text-base-content/50 max-w-xs mx-auto">
            Belum ada rincian foto atau ekspertise radiologi yang dipublikasikan untuk pendaftaran ini.
          </p>
        </div>
        <div class="pt-2">
          <Link href="/rekam-medis/radiologi" class="btn btn-sm btn-outline btn-primary rounded-xl text-xs">
            ← Kembali ke Daftar Radiologi
          </Link>
        </div>
      </div>

      <!-- State: Ada Data -->
      <div v-else class="space-y-3.5">
        <div
          v-for="(rad, idx) in radiologiItems"
          :key="idx"
          class="card bg-base-100 border border-base-300 shadow-xs rounded-3xl p-4 space-y-3.5"
        >
          <!-- Info Pemeriksaan & Dokter -->
          <div class="flex items-start justify-between gap-2 border-b border-base-200/60 pb-3">
            <div class="space-y-0.5">
              <span class="font-bold text-blue-600 text-xs block flex items-center gap-1.5">
                <span>🩻</span> {{ rad.nmItem || rad.pemeriksaan || 'Foto Radiologi' }}
              </span>
              <span v-if="rad.nmDokterInterpretasi || rad.dokter" class="text-[10px] text-base-content/50 block font-medium">
                Dokter: <span class="text-base-content/80">{{ rad.nmDokterInterpretasi || rad.dokter }}</span>
              </span>
            </div>
            <span v-if="rad.kdTrs" class="text-[9px] px-2 py-0.5 bg-base-200 text-base-content/60 rounded-md font-mono shrink-0 font-semibold">
              {{ rad.kdTrs }}
            </span>
          </div>

          <!-- Kesan / Hasil Pembacaan -->
          <div class="bg-base-200/40 p-3.5 rounded-2xl border border-base-200/60 space-y-1">
            <span class="text-[10px] font-bold text-base-content/50 uppercase tracking-wider block">
              Kesan / Interpretasi:
            </span>
            <p class="text-xs font-medium text-base-content leading-relaxed whitespace-pre-line">
              {{ rad.kesan || rad.hasil || '-' }}
            </p>
          </div>

          <!-- Attachment Foto Radiologi -->
          <div v-if="rad.file && rad.file.length > 0" class="space-y-2 pt-1">
            <span class="text-[10px] font-bold text-base-content/50 uppercase tracking-wider block">Lampiran Foto ({{ rad.file.length }})</span>
            <div class="flex gap-2.5 overflow-x-auto pb-1.5">
              <div
                v-for="(imgObj, imgIdx) in rad.file"
                :key="imgIdx"
                @click="openModal(imgObj.file || imgObj)"
                class="relative group cursor-pointer border border-base-300 rounded-2xl overflow-hidden shrink-0 w-20 h-20 bg-black/5 hover:border-blue-500/50 transition-all shadow-2xs"
              >
                <img
                  :src="imgObj.file || imgObj"
                  alt="Foto Radiologi"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div class="absolute inset-0 bg-blue-600/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition-opacity">
                  🔍 Lihat
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
        <div class="relative max-w-lg w-full bg-base-100 rounded-3xl overflow-hidden shadow-2xl border border-base-300">
          <div class="flex items-center justify-between p-3.5 border-b border-base-200 bg-base-100">
            <span class="text-xs font-bold text-base-content">Preview Foto Radiologi</span>
            <button @click="closeModal" class="btn btn-sm btn-circle btn-ghost">✕</button>
          </div>
          <div class="p-3 bg-black flex items-center justify-center max-h-[80vh] overflow-auto">
            <img :src="activeImage" alt="Radiologi Full" class="max-w-full max-h-[75vh] object-contain rounded-xl" />
          </div>
        </div>
      </div>
    </main>
  </AppLayout>
</template>
