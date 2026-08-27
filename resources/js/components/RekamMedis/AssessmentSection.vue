<script setup>
const props = defineProps({
  assesmen: {
    type: Object,
    default: () => ({})
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
  <div v-if="hasContent(assesmen)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-7 h-7 rounded-xl bg-emerald-500/10 text-emerald-600 font-bold text-sm flex items-center justify-center">
        🩺
      </div>
      <div>
        <h3 class="text-xs font-black text-base-content">Assessment (Diagnosis Dokter)</h3>
        <p class="text-[9px] text-base-content/50">Kesimpulan diagnosis klinis & ICD-10</p>
      </div>
    </div>

    <div class="space-y-2 text-xs">
      <!-- List Diagnosis Kerja -->
      <div v-if="hasContent(assesmen.diagKerja)">
        <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Diagnosis Kerja</span>
        <div class="space-y-1.5">
          <div
            v-for="(diag, idx) in assesmen.diagKerja"
            :key="idx"
            class="flex items-center justify-between p-2.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-900 dark:text-emerald-200 font-bold"
          >
            <span class="truncate text-xs">{{ diag.name || diag }}</span>
            <span v-if="diag.kd" class="badge badge-emerald bg-emerald-600 text-white border-0 text-[10px] font-mono shrink-0 ml-2">
              {{ diag.kd }}
            </span>
          </div>
        </div>
      </div>

      <!-- Teks Diagnosis Bebas -->
      <div v-if="hasContent(assesmen.diagKerjaTxt)">
        <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Catatan Diagnosis</span>
        <p class="font-bold text-base-content bg-base-200/50 p-2.5 rounded-2xl border border-base-200 text-[11px] uppercase tracking-wide">
          {{ assesmen.diagKerjaTxt }}
        </p>
      </div>
    </div>
  </div>
</template>
