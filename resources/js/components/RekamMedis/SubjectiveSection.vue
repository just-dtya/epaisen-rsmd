<script setup>
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
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-7 h-7 rounded-xl bg-sky-500/10 text-sky-500 font-bold text-sm flex items-center justify-center">
        💬
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
        <p class="font-bold text-base-content/80 text-[11px] bg-amber-500/10 text-amber-800 dark:text-amber-300 p-2.5 rounded-xl border border-amber-500/20">
          📋 {{ screening.rpt }}
        </p>
      </div>

      <!-- Badges Riwayat Penyakit Terdata -->
      <div v-if="hasContent(riwayatPenyakit)">
        <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Riwayat Penyakit Terdata</span>
        <div class="flex flex-wrap gap-1.5">
          <span
            v-for="(rp, idx) in riwayatPenyakit"
            :key="idx"
            class="badge bg-warning/15 text-warning-content border-warning/30 text-[10px] font-bold py-2 px-2.5 rounded-xl gap-1"
          >
            ⚠️ {{ rp.ket }} <span class="opacity-60">({{ rp.jenisRiwayatPenyakit?.nmJenisRiwayatPenyakit }})</span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
