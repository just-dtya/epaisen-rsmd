<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  id_pendaftaran: {
    type: String,
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
  <Head title="Hasil Laboratorium" />

  <AppLayout>
    <main class="p-4 sm:p-5 max-w-md mx-auto w-full space-y-4 pb-12">
      <!-- Top Navigation Header -->
      <div class="flex items-center justify-between bg-base-100 p-3.5 rounded-3xl border border-base-300 shadow-xs">
        <div class="flex items-center gap-3">
          <Link href="/rekam-medis/lab" class="btn btn-sm btn-circle btn-ghost bg-base-200/60 hover:bg-base-200">
            ←
          </Link>
          <div>
            <h2 class="text-sm font-black text-base-content tracking-tight">Hasil Laboratorium</h2>
            <p class="text-[10px] font-mono text-base-content/50">ID: {{ id_pendaftaran }}</p>
          </div>
        </div>
        <span class="px-2.5 py-1 rounded-full bg-purple-500/10 text-purple-600 text-[10px] font-bold border border-purple-500/20">
          🧪 LAB
        </span>
      </div>

      <!-- State: Error / Data Kosong -->
      <div v-if="error || labItems.length === 0" class="card bg-base-100 border border-base-300 p-10 text-center rounded-3xl space-y-3 shadow-xs">
        <div class="w-14 h-14 bg-purple-500/10 text-purple-600 rounded-2xl flex items-center justify-center mx-auto text-2xl font-bold">
          🧪
        </div>
        <div class="space-y-1">
          <p class="text-xs font-bold text-base-content/80">{{ error || 'Data Laboratorium Kosong' }}</p>
          <p class="text-[11px] text-base-content/50 max-w-xs mx-auto">
            Belum ada rincian hasil pemeriksaan laboratorium yang dipublikasikan untuk pendaftaran ini.
          </p>
        </div>
        <div class="pt-2">
          <Link href="/rekam-medis/lab" class="btn btn-sm btn-outline btn-primary rounded-xl text-xs">
            ← Kembali ke Daftar Lab
          </Link>
        </div>
      </div>

      <!-- State: Ada Data -->
      <div v-else class="space-y-3">
        <div class="flex items-center justify-between px-1">
          <h3 class="text-xs font-bold text-base-content/70 uppercase tracking-wider">Rincian Hasil Pemeriksaan</h3>
          <span class="text-[10px] text-base-content/50 font-medium">{{ labItems.length }} Parameter Diperiksa</span>
        </div>

        <div class="card bg-base-100 border border-base-300 shadow-xs rounded-3xl p-4 divide-y divide-base-200/60">
          <div
            v-for="(lab, idx) in labItems"
            :key="idx"
            class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-3"
          >
            <!-- Nama Item & Nilai Rujukan -->
            <div class="space-y-0.5 flex-1">
              <span class="font-bold text-base-content text-xs block">
                {{ lab.namaPemeriksaan || lab.nmItem || lab.item || 'Pemeriksaan Lab' }}
              </span>
              <span v-if="lab.nilaiRujukan" class="text-[10px] text-base-content/50 block font-medium">
                Rujukan: <span class="font-mono">{{ lab.nilaiRujukan }}</span>
              </span>
            </div>

            <!-- Hasil & Satuan -->
            <div class="text-right shrink-0">
              <div class="flex items-baseline justify-end gap-1">
                <span class="font-black text-purple-600 text-sm">
                  {{ lab.hasilPemeriksaan || lab.hasil || lab.nilai || '-' }}
                </span>
                <span class="text-[10px] text-base-content/60 font-semibold">
                  {{ lab.namaSatuanPemeriksaan || lab.satuan || '' }}
                </span>
              </div>

              <!-- Keterangan Status Hasil -->
              <span
                v-if="lab.ketHasil"
                :class="{
                  'bg-error/10 text-error border-error/20': ['H', 'High', 'Positif', '+'].includes(lab.ketHasil),
                  'bg-success/10 text-success border-success/20': ['L', 'Low', 'Normal'].includes(lab.ketHasil)
                }"
                class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold mt-1 border"
              >
                {{ lab.ketHasil }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </AppLayout>
</template>
