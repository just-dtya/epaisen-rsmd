<script setup>
import { computed } from 'vue'

const props = defineProps({
  screening: {
    type: Object,
    default: () => ({})
  },
  subjective: {
    type: Object,
    default: () => ({})
  },
  riwayatPenyakit: {
    type: Array,
    default: () => []
  }
})

const hasContent = (val) => {
  if (val === null || val === undefined || val === '' || val === false) return false
  if (typeof val === 'number') return val !== 0
  if (typeof val === 'string') {
    const trimmed = val.trim()
    return trimmed !== '' && trimmed !== '0' && trimmed !== '0.00'
  }
  if (Array.isArray(val)) return val.length > 0
  return true
}
</script>

<template>
  <div v-if="hasContent(screening) || hasContent(subjective)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
    <!-- Header -->
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center shrink-0">
        <!-- Ikon Chat Bubble / Anamnesis -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
      </div>
      <div>
        <h3 class="text-xs font-black text-base-content">Subjective (Keluhan & Anamnesis)</h3>
        <p class="text-[9px] text-base-content/50">Keluhan utama & catatan riwayat kesehatan</p>
      </div>
    </div>

    <div class="space-y-2.5 text-xs">
      <!-- Keluhan Utama dari Screening -->
      <div v-if="hasContent(screening.aap)">
        <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Keluhan Utama</span>
        <div class="p-3 rounded-2xl bg-sky-500/5 border border-sky-500/20 font-bold text-sky-950 dark:text-sky-200 leading-relaxed">
          "{{ screening.aap }}"
        </div>
      </div>

      <!-- Anamnesa Dokter -->
      <div v-if="hasContent(subjective?.anamnesa)">
        <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Anamnesa Dokter</span>
        <p class="font-medium text-base-content bg-base-200/50 p-3 rounded-2xl border border-base-200 leading-relaxed">
          {{ subjective.anamnesa }}
        </p>
      </div>

      <!-- Riwayat Penyakit Terkait -->
      <div v-if="hasContent(screening.rpt)">
        <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Riwayat Penyakit Terkait</span>
        <div class="flex items-start gap-2 font-bold text-base-content/80 text-[11px] bg-amber-500/10 text-amber-800 dark:text-amber-300 p-2.5 rounded-xl border border-amber-500/20">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span class="leading-snug">{{ screening.rpt }}</span>
        </div>
      </div>

      <!-- Badges Riwayat Penyakit Terdata -->
      <div v-if="hasContent(riwayatPenyakit)">
        <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Riwayat Penyakit Terdata</span>
        <div class="flex flex-wrap gap-1.5">
          <span
            v-for="(rp, idx) in riwayatPenyakit"
            :key="idx"
            class="badge bg-warning/15 text-warning-content border-warning/30 text-[10px] font-bold py-2.5 px-2.5 rounded-xl gap-1.5 inline-flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ rp.ket }} <span class="opacity-60 font-normal">({{ rp.jenisRiwayatPenyakit?.nmJenisRiwayatPenyakit }})</span></span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
