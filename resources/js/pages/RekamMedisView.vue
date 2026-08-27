<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  id_pendaftaran: String,
  rekam_medis: {
    type: Object,
    default: () => ({})
  },
  error: {
    type: String,
    default: null
  }
})

// Helper untuk mengecek apakah sebuah nilai dianggap "ada isinya"
const hasContent = (val) => {
  if (val === null || val === undefined || val === '' || val === false) return false
  if (typeof val === 'number') return val !== 0
  if (typeof val === 'string') {
    const trimmed = val.trim()
    return trimmed !== '' && trimmed !== '0' && trimmed !== '0.00'
  }
  if (Array.isArray(val)) return val.length > 0
  if (typeof val === 'object') {
    return Object.values(val).some(v => hasContent(v))
  }
  return true
}

// Helper untuk menyaring properti objek yang hanya memiliki isi
const filterFilledObject = (obj) => {
  if (!obj || typeof obj !== 'object') return {}
  return Object.entries(obj).reduce((acc, [key, val]) => {
    if (hasContent(val)) {
      acc[key] = val
    }
    return acc
  }, {})
}
</script>

<template>
  <Head title="Detail Rekam Medis" />

  <AppLayout>
    <main class="p-4 space-y-3.5 max-w-md mx-auto w-full select-none pb-20">

      <!-- Top Navigation -->
      <div class="flex items-center justify-between bg-base-100 p-3 rounded-2xl border border-base-300 shadow-2xs">
        <div class="flex items-center gap-3">
          <Link href="/riwayat" class="btn btn-sm btn-circle btn-ghost">
            ✕
          </Link>
          <div>
            <h2 class="text-sm font-black text-base-content">Detail Rekam Medis</h2>
            <p class="text-[10px] font-mono text-base-content/60">ID: {{ id_pendaftaran }}</p>
          </div>
        </div>
        <div class="badge badge-primary badge-outline text-[10px] font-mono">ERM SOAP</div>
      </div>

      <!-- State: Error -->
      <div v-if="error" class="card bg-error/10 border border-error/20 p-5 rounded-3xl text-center space-y-2">
        <p class="text-3xl">⚠️</p>
        <p class="text-xs font-bold text-error">{{ error }}</p>
        <p class="text-[10px] text-base-content/60">Gagal memuat rekam medis pasien dari server ERM.</p>
        <div class="pt-2">
          <Link href="/riwayat" class="btn btn-xs btn-error text-white rounded-xl font-bold">
            Kembali ke Riwayat
          </Link>
        </div>
      </div>

      <!-- State: Data Terload -->
      <div v-else-if="rekam_medis && hasContent(rekam_medis)" class="space-y-3.5">

        <!-- 1. SUBJECTIVE (S) -->
        <div v-if="hasContent(rekam_medis.subjective)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-2.5">
          <div class="flex items-center justify-between border-b border-base-200 pb-2">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-lg bg-info/10 text-info font-black text-xs flex items-center justify-center">S</span>
              <h3 class="text-xs font-bold text-base-content">Subjective (Anamnesis)</h3>
            </div>
            <span v-if="hasContent(rekam_medis.subjective.tanggal)" class="text-[10px] text-base-content/50">
              {{ rekam_medis.subjective.tanggal }} {{ rekam_medis.subjective.jam || '' }}
            </span>
          </div>

          <div class="space-y-2 text-xs">
            <div v-if="hasContent(rekam_medis.subjective.anamnesa)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">Anamnesa / Keluhan</span>
              <p class="font-semibold text-base-content bg-base-200/50 p-2.5 rounded-xl border border-base-300/40 mt-0.5">
                {{ rekam_medis.subjective.anamnesa }}
              </p>
            </div>

            <div v-if="hasContent(rekam_medis.subjective.instruksi)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">Instruksi Dokter</span>
              <p class="font-bold text-primary bg-primary/5 p-2.5 rounded-xl border border-primary/20 mt-0.5">
                📌 {{ rekam_medis.subjective.instruksi }}
              </p>
            </div>

            <div v-if="hasContent(rekam_medis.subjective.rps)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">Riwayat Penyakit Sekarang (RPS)</span>
              <p class="font-medium text-base-content mt-0.5">{{ rekam_medis.subjective.rps }}</p>
            </div>

            <div v-if="hasContent(rekam_medis.subjective.kesimpulan)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">Kesimpulan</span>
              <p class="font-medium text-base-content mt-0.5">{{ rekam_medis.subjective.kesimpulan }}</p>
            </div>

            <!-- Field Subjective Tambahan Lainnya yang Berisi -->
            <template v-for="(val, key) in filterFilledObject(rekam_medis.subjective)" :key="key">
              <div v-if="!['stsTandai','tanggal','jam','anamnesa','instruksi','rps','kesimpulan'].includes(key)" class="pt-1">
                <span class="text-[10px] text-base-content/50 uppercase font-bold block capitalize">{{ key.replace(/([A-Z])/g, ' $1') }}</span>
                <span class="font-bold text-base-content text-[11px]">{{ val }}</span>
              </div>
            </template>
          </div>
        </div>

        <!-- 2. OBJECTIVE (O) -->
        <div v-if="hasContent(rekam_medis.objective)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-2.5">
          <div class="flex items-center gap-2 border-b border-base-200 pb-2">
            <span class="w-6 h-6 rounded-lg bg-warning/10 text-warning font-black text-xs flex items-center justify-center">O</span>
            <h3 class="text-xs font-bold text-base-content">Objective (Pemeriksaan)</h3>
          </div>

          <div class="space-y-3 text-xs">
            <!-- TTV (Tanda-Tanda Vital) -->
            <div v-if="hasContent(rekam_medis.objective.ttv)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Tanda-Tanda Vital</span>
              <div class="grid grid-cols-2 gap-2 bg-base-200/40 p-2.5 rounded-2xl border border-base-200 text-[10px]">
                <div v-if="hasContent(rekam_medis.objective.ttv.sistolik)">
                  <span class="text-base-content/50 block">Tekanan Darah</span>
                  <span class="font-bold text-xs">{{ rekam_medis.objective.ttv.sistolik }}/{{ rekam_medis.objective.ttv.diastolik }} mmHg</span>
                </div>
                <div v-if="hasContent(rekam_medis.objective.ttv.nadi)">
                  <span class="text-base-content/50 block">Nadi</span>
                  <span class="font-bold text-xs">{{ rekam_medis.objective.ttv.nadi }} x/menit</span>
                </div>
                <div v-if="hasContent(rekam_medis.objective.ttv.suhu)">
                  <span class="text-base-content/50 block">Suhu</span>
                  <span class="font-bold text-xs">{{ rekam_medis.objective.ttv.suhu }} °C</span>
                </div>
                <div v-if="hasContent(rekam_medis.objective.ttv.napas)">
                  <span class="text-base-content/50 block">Napas</span>
                  <span class="font-bold text-xs">{{ rekam_medis.objective.ttv.napas }} x/menit</span>
                </div>
              </div>
            </div>

            <!-- Pemeriksaan Spesialis (Mata, THT, Gigi, Dll) - Otomatis hanya yang berisi -->
            <template v-for="(specData, specKey) in rekam_medis.objective" :key="specKey">
              <div v-if="specKey !== 'ttv' && hasContent(specData)" class="border-t border-base-200 pt-2 space-y-1">
                <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">
                  Pemeriksaan {{ specKey.replace(/([A-Z])/g, ' $1') }}
                </span>
                <div class="bg-base-200/40 p-2.5 rounded-2xl border border-base-200 space-y-1">
                  <div v-for="(val, fieldKey) in filterFilledObject(specData)" :key="fieldKey" class="text-[11px]">
                    <span class="text-base-content/60 font-bold capitalize">{{ fieldKey.replace(/([A-Z])/g, ' $1') }}:</span>
                    <span class="font-bold text-primary ml-1">{{ typeof val === 'object' ? JSON.stringify(val) : val }}</span>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- 3. ASSESMEN (A) -->
        <div v-if="hasContent(rekam_medis.assesmen)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-2.5">
          <div class="flex items-center gap-2 border-b border-base-200 pb-2">
            <span class="w-6 h-6 rounded-lg bg-success/10 text-success font-black text-xs flex items-center justify-center">A</span>
            <h3 class="text-xs font-bold text-base-content">Assessment (Diagnosis)</h3>
          </div>

          <div class="space-y-2 text-xs">
            <!-- Diagnosis Kerja ICD-10 -->
            <div v-if="hasContent(rekam_medis.assesmen.diagKerja)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Diagnosis Kerja (ICD-10)</span>
              <div class="space-y-1">
                <div
                  v-for="diag in rekam_medis.assesmen.diagKerja"
                  :key="diag.id"
                  class="flex items-center justify-between p-2 rounded-xl bg-success/5 border border-success/20 text-success font-bold"
                >
                  <span class="truncate">{{ diag.name }}</span>
                  <span class="badge badge-success text-[10px] font-mono text-white shrink-0 ml-2">{{ diag.kd }}</span>
                </div>
              </div>
            </div>

            <!-- Diagnosis Sekunder -->
            <div v-if="hasContent(rekam_medis.assesmen.diagSekunder)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Diagnosis Sekunder</span>
              <div class="space-y-1">
                <div
                  v-for="diag in rekam_medis.assesmen.diagSekunder"
                  :key="diag.id"
                  class="flex items-center justify-between p-2 rounded-xl bg-base-200 border border-base-300 text-base-content font-bold"
                >
                  <span class="truncate">{{ diag.name }}</span>
                  <span class="badge badge-ghost text-[10px] font-mono shrink-0 ml-2">{{ diag.kd }}</span>
                </div>
              </div>
            </div>

            <!-- Keterangan Diagnosis -->
            <div v-if="hasContent(rekam_medis.assesmen.diagKerjaTxt)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">Keterangan Diagnosis</span>
              <p class="font-medium text-base-content mt-0.5 uppercase tracking-wide">
                {{ rekam_medis.assesmen.diagKerjaTxt }}
              </p>
            </div>
          </div>
        </div>

        <!-- 4. PLANNING (P) -->
        <div v-if="hasContent(rekam_medis.planning)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-2.5">
          <div class="flex items-center gap-2 border-b border-base-200 pb-2">
            <span class="w-6 h-6 rounded-lg bg-secondary/10 text-secondary font-black text-xs flex items-center justify-center">P</span>
            <h3 class="text-xs font-bold text-base-content">Planning & Resep</h3>
          </div>

          <div class="space-y-2 text-xs">
            <!-- Resep Obat -->
            <div v-if="hasContent(rekam_medis.planning.resep)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Resep Obat</span>
              <div class="space-y-1">
                <div v-for="(item, idx) in rekam_medis.planning.resep" :key="idx" class="p-2 rounded-xl bg-base-200/50 border border-base-300">
                  <p class="font-bold text-base-content">{{ item.nama_obat || item.nama }}</p>
                  <p v-if="hasContent(item.aturan_pakai || item.dosis)" class="text-[10px] text-base-content/60">
                    {{ item.aturan_pakai || item.dosis }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Catatan Alergi -->
            <div v-if="hasContent(rekam_medis.planning.alergi) || hasContent(rekam_medis.planning.alergiObat) || hasContent(rekam_medis.planning.alergiMakanan)" class="bg-warning/10 p-2.5 rounded-2xl border border-warning/20">
              <span class="text-[10px] font-bold text-warning uppercase block">⚠️ Catatan Alergi</span>
              <p class="text-[11px] font-semibold text-base-content">
                {{ [rekam_medis.planning.alergi, rekam_medis.planning.alergiObat, rekam_medis.planning.alergiMakanan].filter(hasContent).join(', ') }}
              </p>
            </div>
          </div>
        </div>

        <!-- 5. ORDER PENUNJANG (ORDER) -->
        <div v-if="hasContent(rekam_medis.order)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-2.5">
          <div class="flex items-center gap-2 border-b border-base-200 pb-2">
            <span class="w-6 h-6 rounded-lg bg-accent/10 text-accent font-black text-xs flex items-center justify-center">O</span>
            <h3 class="text-xs font-bold text-base-content">Order Penunjang</h3>
          </div>

          <div class="space-y-3 text-xs">
            <!-- Order Laboratorium -->
            <div v-if="hasContent(rekam_medis.order.lab?.item)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Laboratorium</span>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="lab in rekam_medis.order.lab.item"
                  :key="lab.id"
                  class="badge badge-ghost border border-base-300 text-[10px] font-medium py-2 px-2.5"
                >
                  🧪 {{ lab.name }}
                </span>
              </div>
            </div>

            <!-- Order Radiologi -->
            <div v-if="hasContent(rekam_medis.order.rad?.item)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Radiologi</span>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="rad in rekam_medis.order.rad.item"
                  :key="rad.id"
                  class="badge badge-ghost border border-base-300 text-[10px] font-medium py-2 px-2.5"
                >
                  🩻 {{ rad.name }}
                </span>
              </div>
            </div>

            <!-- Order Penunjang Diagnostik -->
            <div v-if="hasContent(rekam_medis.order.penunjangDiagnostik?.biometry)">
              <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block mb-1">Penunjang Diagnostik</span>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="(bioCode, idx) in rekam_medis.order.penunjangDiagnostik.biometry"
                  :key="idx"
                  class="badge badge-primary text-[10px] font-bold text-white py-2 px-2.5"
                >
                  🔬 Biometri: {{ bioCode }}
                </span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- State: Empty Data -->
      <div v-else class="card bg-base-100 border border-base-300 p-8 text-center rounded-3xl space-y-2">
        <p class="text-3xl">📭</p>
        <p class="text-xs font-bold text-base-content/70">Data Rekam Medis Kosong</p>
        <p class="text-[11px] text-base-content/50">Tidak ada rincian pemeriksaan SOAP yang terisi untuk pendaftaran ini.</p>
      </div>

    </main>
  </AppLayout>
</template>
