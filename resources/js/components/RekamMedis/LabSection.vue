<script setup>
import { computed } from 'vue'

const props = defineProps({
  hasilLab: {
    type: [Array, Object],
    default: () => []
  }
})

// Normalisasi agar selalu berbentuk Array item
const labItems = computed(() => {
  if (!props.hasilLab) return []
  if (Array.isArray(props.hasilLab)) return props.hasilLab
  if (typeof props.hasilLab === 'object') {
    return props.hasilLab.itemsPemeriksaanBerkasPasienRujukanDalamPenunjang ||
           props.hasilLab.items ||
           props.hasilLab.hasilLab ||
           []
  }
  return []
})
</script>

<template>
  <div v-if="labItems.length > 0" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
    <!-- Header Lab -->
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-7 h-7 rounded-xl bg-purple-500/10 text-purple-500 font-bold text-sm flex items-center justify-center">
        🧪
      </div>
      <div>
        <h3 class="text-xs font-black text-base-content">Hasil Laboratorium</h3>
        <p class="text-[9px] text-base-content/50">Pemeriksaan darah & laboratorium medis</p>
      </div>
    </div>

    <!-- Items Lab -->
    <div class="bg-base-200/40 p-3 rounded-2xl border border-base-200 space-y-2.5 text-xs">
      <div
        v-for="(lab, idx) in labItems"
        :key="idx"
        class="flex items-center justify-between border-b border-base-200/60 pb-2 last:border-0 last:pb-0"
      >
        <div class="space-y-0.5">
          <span class="font-bold text-base-content text-[11px] block">
            {{ lab.namaPemeriksaan || lab.nmItem || lab.item || 'Pemeriksaan Lab' }}
          </span>
          <span class="text-[9px] text-base-content/50 block" v-if="lab.nilaiRujukan">
            Rujukan: {{ lab.nilaiRujukan }}
          </span>
        </div>

        <div class="text-right">
          <span class="font-black text-primary text-xs block">
            {{ lab.hasilPemeriksaan || lab.hasil || lab.nilai || '-' }} {{ lab.namaSatuanPemeriksaan || lab.satuan || '' }}
          </span>
          <span v-if="lab.ketHasil" class="text-[8px] text-base-content/40 block">
            {{ lab.ketHasil }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
