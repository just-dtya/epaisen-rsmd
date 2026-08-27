<script setup>
import { computed } from 'vue'

const props = defineProps({
  ttv: {
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
  <div v-if="hasContent(ttv)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
    <div class="flex items-center justify-between border-b border-base-200 pb-2.5">
      <div class="flex items-center gap-2">
        <div class="w-7 h-7 rounded-xl bg-rose-500/10 text-rose-500 font-bold text-sm flex items-center justify-center">
          ❤️
        </div>
        <div>
          <h3 class="text-xs font-black text-base-content">Tanda-Tanda Vital (TTV)</h3>
          <p class="text-[9px] text-base-content/50">Pemeriksaan fisik awal perawat</p>
        </div>
      </div>
      <span v-if="ttv.tanggal" class="text-[10px] font-mono font-medium text-base-content/60 bg-base-200 px-2 py-0.5 rounded-md">
        {{ ttv.tanggal }} {{ ttv.jam || '' }}
      </span>
    </div>

    <!-- Grid Metrics TTV -->
    <div class="grid grid-cols-3 gap-2">
      <!-- Tekanan Darah -->
      <div v-if="hasContent(ttv.sistolik)" class="col-span-3 sm:col-span-1 bg-rose-500/5 p-3 rounded-2xl border border-rose-500/20 flex flex-col justify-between">
        <span class="text-[9px] font-bold text-rose-600/70 uppercase">Tekanan Darah</span>
        <div class="mt-1">
          <span class="text-base font-black text-rose-600">{{ ttv.sistolik }}/{{ ttv.diastolik }}</span>
          <span class="text-[9px] text-rose-600/70 ml-1">mmHg</span>
        </div>
      </div>

      <!-- Nadi -->
      <div v-if="hasContent(ttv.nadi)" class="bg-base-200/50 p-2.5 rounded-2xl border border-base-200">
        <span class="text-[9px] font-bold text-base-content/50 uppercase block">Nadi</span>
        <span class="text-xs font-black text-base-content mt-0.5 block">{{ ttv.nadi }} <span class="text-[9px] font-normal text-base-content/60">x/m</span></span>
      </div>

      <!-- Suhu -->
      <div v-if="hasContent(ttv.suhu)" class="bg-base-200/50 p-2.5 rounded-2xl border border-base-200">
        <span class="text-[9px] font-bold text-base-content/50 uppercase block">Suhu Body</span>
        <span class="text-xs font-black text-base-content mt-0.5 block">{{ ttv.suhu }} <span class="text-[9px] font-normal text-base-content/60">°C</span></span>
      </div>

      <!-- Napas -->
      <div v-if="hasContent(ttv.napas)" class="bg-base-200/50 p-2.5 rounded-2xl border border-base-200">
        <span class="text-[9px] font-bold text-base-content/50 uppercase block">Laju Napas</span>
        <span class="text-xs font-black text-base-content mt-0.5 block">{{ ttv.napas }} <span class="text-[9px] font-normal text-base-content/60">x/m</span></span>
      </div>

      <!-- Tinggi & Berat -->
      <div v-if="hasContent(ttv.tb)" class="col-span-2 bg-base-200/50 p-2.5 rounded-2xl border border-base-200 flex justify-between items-center">
        <div>
          <span class="text-[9px] font-bold text-base-content/50 uppercase block">Tinggi / Berat</span>
          <span class="text-xs font-black text-base-content mt-0.5 block">{{ ttv.tb }} cm / {{ ttv.bb }} kg</span>
        </div>
        <span class="text-lg">⚖️</span>
      </div>

      <!-- Saturasi O2 -->
      <div v-if="hasContent(ttv.saturasiOksigen)" class="bg-emerald-500/10 p-2.5 rounded-2xl border border-emerald-500/20">
        <span class="text-[9px] font-bold text-emerald-700/70 uppercase block">Saturasi O2</span>
        <span class="text-xs font-black text-emerald-600 mt-0.5 block">{{ ttv.saturasiOksigen }}%</span>
      </div>
    </div>

    <div v-if="hasContent(ttv.petugasAnamnesaNama)" class="text-[9px] text-base-content/40 text-right italic pt-1">
      Petugas: {{ ttv.petugasAnamnesaNama }}
    </div>
  </div>
</template>
