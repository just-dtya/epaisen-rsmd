<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Import Sub-Components (Path disesuaikan menggunakan 'components' huruf kecil)
import TtvSection from '@/components/RekamMedis/TtvSection.vue'
import SubjectiveSection from '@/components/RekamMedis/SubjectiveSection.vue'
import ObjectiveSection from '@/components/RekamMedis/ObjectiveSection.vue'
import LabSection from '@/components/RekamMedis/LabSection.vue'
import RadiologiSection from '@/components/RekamMedis/RadiologiSection.vue'
import AssessmentSection from '@/components/RekamMedis/AssessmentSection.vue'
import PlanningSection from '@/components/RekamMedis/PlanningSection.vue'

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

// Helper untuk validasi kelayakan isi data
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

// Computed Data Map dengan Dual Key Fallback
const soap = computed(() => props.rekam_medis?.soap_dokter || props.rekam_medis?.soapDokter || {})
const perawat = computed(() => props.rekam_medis?.perawat_pemeriksaan || props.rekam_medis?.perawatPemeriksaan || {})
const ttv = computed(() => props.rekam_medis?.perawat_ttv || props.rekam_medis?.perawatTtv || perawat.value?.pemeriksaanFisik || {})
const screening = computed(() => perawat.value?.screening || {})
const refraksi = computed(() => props.rekam_medis?.refraksi || {})
const penunjangKlinik = computed(() => perawat.value?.penunjangKlinik || {})
const riwayatPenyakit = computed(() => props.rekam_medis?.riwayat_penyakit || props.rekam_medis?.riwayatPenyakit || [])

// Fallback Fleksibel Khusus Penunjang Lab & Radiologi
const hasilLab = computed(() => {
  return props.rekam_medis?.hasilLab ?? props.rekam_medis?.hasil_lab ?? []
})

const hasilRadiologi = computed(() => {
  return props.rekam_medis?.hasilRadiologi ?? props.rekam_medis?.hasil_radiologi ?? []
})
</script>

<template>
  <Head title="Detail Rekam Medis" />

  <AppLayout>
    <main class="p-3.5 sm:p-5 space-y-4 max-w-lg mx-auto w-full select-none pb-24">

      <!-- Top Navigation Header -->
      <div class="flex items-center justify-between bg-base-100 p-3.5 rounded-3xl border border-base-300 shadow-2xs">
        <div class="flex items-center gap-3">
          <Link href="/riwayat" class="btn btn-sm btn-circle btn-ghost bg-base-200/60 hover:bg-base-200">
            ✕
          </Link>
          <div>
            <h2 class="text-sm font-black text-base-content tracking-tight">Detail Rekam Medis</h2>
            <p class="text-[10px] font-mono text-base-content/50">ID: {{ id_pendaftaran }}</p>
          </div>
        </div>
        <span class="px-2.5 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-bold border border-primary/20">
          ERM DIGITAL
        </span>
      </div>

      <!-- State Error -->
      <div v-if="error" class="card bg-error/10 border border-error/20 p-6 rounded-3xl text-center space-y-2">
        <div class="w-12 h-12 bg-error/20 text-error rounded-2xl flex items-center justify-center mx-auto text-xl font-bold">
          ⚠️
        </div>
        <p class="text-xs font-bold text-error">{{ error }}</p>
        <p class="text-[10px] text-base-content/60">Gagal memuat rekam medis pasien dari server ERM.</p>
        <div class="pt-2">
          <Link href="/riwayat" class="btn btn-xs btn-error text-white rounded-xl font-bold px-4">
            Kembali ke Riwayat
          </Link>
        </div>
      </div>

      <!-- State: Data Terload -->
      <div v-else-if="hasContent(rekam_medis)" class="space-y-3.5">
        <!-- 1. Section TTV -->
        <TtvSection :ttv="ttv" />

        <!-- 2. Section Subjective (Anamnesis) -->
        <SubjectiveSection
          :screening="screening"
          :subjective="soap.subjective"
          :riwayatPenyakit="riwayatPenyakit"
        />

        <!-- 3. Section Objective (Refraksi & Tonometri) -->
        <ObjectiveSection
          :penunjangKlinik="penunjangKlinik"
          :refraksi="refraksi"
        />

        <!-- 4. Section Laboratorium Terpisah -->
        <LabSection :hasilLab="hasilLab" />

        <!-- 5. Section Radiologi Terpisah -->
        <RadiologiSection :hasilRadiologi="hasilRadiologi" />

        <!-- 6. Section Assessment (Diagnosis) -->
        <AssessmentSection :assesmen="soap.assesmen" />

        <!-- 7. Section Planning & Implementasi -->
        <PlanningSection :implementasi="screening.implementasiRawat" />
      </div>

      <!-- State: Data Kosong -->
      <div v-else class="card bg-base-100 border border-base-300 p-8 text-center rounded-3xl space-y-2">
        <div class="w-12 h-12 bg-base-200 text-base-content/40 rounded-2xl flex items-center justify-center mx-auto text-xl">
          📭
        </div>
        <p class="text-xs font-bold text-base-content/80">Data Rekam Medis Kosong</p>
        <p class="text-[11px] text-base-content/50 max-w-xs mx-auto">
          Belum ada rincian pemeriksaan SOAP atau data belum dipublikasikan untuk pendaftaran ini.
        </p>
      </div>

    </main>
  </AppLayout>
</template>
