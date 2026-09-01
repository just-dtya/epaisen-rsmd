<script setup>
import { computed } from 'vue'

const props = defineProps({
  idPendaftaran: {
    type: [String, Number],
    required: true
  },
  hasilLab: {
    type: [Array, Object],
    default: () => []
  },
  error: {
    type: String,
    default: null
  }
})

// Normalisasi data laboratorium agar selalu berupa Array item
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
  <div class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center gap-3 border-b border-base-200 pb-3">
      <div class="w-9 h-9 rounded-2xl bg-purple-500/10 text-purple-600 font-bold text-base flex items-center justify-center shrink-0">
        🧪
      </div>
      <div>
        <h3 class="text-sm font-black text-base-content">Hasil Laboratorium</h3>
        <p class="text-[10px] text-base-content/50">Detail hasil tes dan rujukan laboratorium pasien</p>
      </div>
    </div>

    <!-- State: Error / Data Kosong -->
    <div v-if="error || labItems.length === 0" class="py-8 text-center space-y-1">
      <div class="text-2xl">📋</div>
      <p class="text-xs font-bold text-base-content/70">
        {{ error || 'Belum ada data hasil laboratorium' }}
      </p>
      <p class="text-[10px] text-base-content/40">Data laboratorium untuk pendaftaran ini tidak tersedia.</p>
    </div>

    <!-- State: Ada Data -->
    <div v-else class="space-y-2">
      <div class="bg-base-200/40 p-3.5 rounded-2xl border border-base-200 divide-y divide-base-200/60">
        <div
          v-for="(lab, idx) in labItems"
          :key="idx"
          class="flex items-center justify-between py-2 first:pt-0 last:pb-0"
        >
          <div class="pr-2">
            <span class="font-bold text-base-content text-xs block">
              {{ lab.namaPemeriksaan || lab.nmItem || lab.item || 'Pemeriksaan Lab' }}
            </span>
            <span v-if="lab.nilaiRujukan" class="text-[10px] text-base-content/50 block mt-0.5">
              Rujukan: {{ lab.nilaiRujukan }}
            </span>
          </div>

          <div class="text-right shrink-0">
            <span class="font-black text-primary text-xs block">
              {{ lab.hasilPemeriksaan || lab.hasil || lab.nilai || '-' }} {{ lab.namaSatuanPemeriksaan || lab.satuan || '' }}
            </span>
            <span v-if="lab.ketHasil" class="text-[9px] text-base-content/50 block mt-0.5">
              {{ lab.ketHasil }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
