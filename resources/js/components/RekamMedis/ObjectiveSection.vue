<script setup>
const props = defineProps({
  penunjangKlinik: {
    type: Object,
    default: () => ({})
  },
  refraksi: {
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
  return true
}
</script>

<template>
  <div v-if="hasContent(penunjangKlinik) || hasContent(refraksi)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-7 h-7 rounded-xl bg-amber-500/10 text-amber-500 font-bold text-sm flex items-center justify-center">
        👁️
      </div>
      <div>
        <h3 class="text-xs font-black text-base-content">Pemeriksaan Khusus & Refraksi</h3>
        <p class="text-[9px] text-base-content/50">Hasil NCT & pemeriksaan mata spesifik</p>
      </div>
    </div>

    <div class="space-y-2.5 text-xs">
      <!-- Non-Contact Tonometry (NCT) -->
      <div v-if="hasContent(penunjangKlinik.odNCT) || hasContent(penunjangKlinik.osNCT)" class="bg-base-200/40 p-3 rounded-2xl border border-base-200 space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-bold text-base-content/70 uppercase tracking-wider">NCT (Tekanan Bola Mata)</span>
          <span v-if="penunjangKlinik.jamNCT" class="text-[9px] font-mono text-base-content/50">Jam: {{ penunjangKlinik.jamNCT }}</span>
        </div>

        <div class="grid grid-cols-2 gap-2 text-center">
          <div class="p-2 bg-base-100 rounded-xl border border-base-300/60">
            <span class="text-[9px] text-base-content/50 uppercase font-bold block">OD (Mata Kanan)</span>
            <span class="text-sm font-black text-primary block mt-0.5">{{ penunjangKlinik.odNCT || '-' }} <span class="text-[9px] font-normal">mmHg</span></span>
          </div>

          <div class="p-2 bg-base-100 rounded-xl border border-base-300/60">
            <span class="text-[9px] text-base-content/50 uppercase font-bold block">OS (Mata Kiri)</span>
            <span class="text-sm font-black text-primary block mt-0.5">{{ penunjangKlinik.osNCT || '-' }} <span class="text-[9px] font-normal">mmHg</span></span>
          </div>
        </div>

        <div v-if="penunjangKlinik.jamTetesMidri" class="text-[9px] text-base-content/60 bg-amber-500/10 p-2 rounded-xl border border-amber-500/20 text-center font-medium">
          💧 Tetes Midriatik dilakukan jam: <strong>{{ penunjangKlinik.jamTetesMidri }}</strong>
        </div>
      </div>
    </div>
  </div>
</template>
