<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Import Sub-Components Rekam Medis
import TtvSection from '@/components/RekamMedis/TtvSection.vue'
import SubjectiveSection from '@/components/RekamMedis/SubjectiveSection.vue'
import ObjectiveSection from '@/components/RekamMedis/ObjectiveSection.vue'
import AssessmentSection from '@/components/RekamMedis/AssessmentSection.vue'
import PlanningSection from '@/components/RekamMedis/PlanningSection.vue'

const props = defineProps({
  id_pendaftaran: {
    type: String,
    required: true
  },
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

// Computed Data Map dengan Dual Key Fallback (Snake & Camel Case)
const soap = computed(() => props.rekam_medis?.soap_dokter || props.rekam_medis?.soapDokter || {})
const perawat = computed(() => props.rekam_medis?.perawat_pemeriksaan || props.rekam_medis?.perawatPemeriksaan || {})
const ttv = computed(() => props.rekam_medis?.perawat_ttv || props.rekam_medis?.perawatTtv || perawat.value?.pemeriksaanFisik || {})
const screening = computed(() => perawat.value?.screening || {})
const refraksi = computed(() => props.rekam_medis?.refraksi || {})
const penunjangKlinik = computed(() => perawat.value?.penunjangKlinik || {})
const riwayatPenyakit = computed(() => props.rekam_medis?.riwayat_penyakit || props.rekam_medis?.riwayatPenyakit || [])
</script>

<template>
  <Head title="Detail Rekam Medis" />

  <AppLayout>
    <main class="p-3.5 sm:p-5 space-y-4 max-w-lg mx-auto w-full select-none pb-24">

      <!-- Top Navigation Header -->
      <div class="flex items-center justify-between bg-base-100 p-3.5 rounded-3xl border border-base-300 shadow-2xs">
        <div class="flex items-center gap-3">
          <Link href="/riwayat" class="btn btn-sm btn-circle btn-ghost bg-base-200/60 hover:bg-base-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
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
        <div class="w-10 h-10 bg-error/20 text-error rounded-2xl flex items-center justify-center mx-auto">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
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

        <!-- Shortcut Menu Penunjang (Laboratorium & Radiologi Terpisah) -->
        <div class="grid grid-cols-2 gap-2.5">
          <Link
            :href="`/rekam-medis/lab/${id_pendaftaran}`"
            class="flex items-center gap-3 p-3 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 rounded-2xl transition-all group"
          >
            <div class="w-9 h-9 rounded-xl bg-purple-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
            </div>
            <div class="overflow-hidden">
              <span class="text-xs font-bold text-base-content block truncate group-hover:text-purple-600">Hasil Lab</span>
              <span class="text-[9px] text-base-content/50 block">Lihat penunjang</span>
            </div>
          </Link>

          <Link
            :href="`/rekam-medis/radiologi/${id_pendaftaran}`"
            class="flex items-center gap-3 p-3 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 rounded-2xl transition-all group"
          >
            <div class="w-9 h-9 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="overflow-hidden">
              <span class="text-xs font-bold text-base-content block truncate group-hover:text-blue-600">Radiologi</span>
              <span class="text-[9px] text-base-content/50 block">Hasil ekspertise</span>
            </div>
          </Link>
        </div>

        <!-- 1. Section TTV (Tanda-tanda Vital) -->
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

        <!-- 4. Section Assessment (Diagnosis Dokter) -->
        <AssessmentSection :assesmen="soap.assesmen" />

        <!-- 5. Section Planning & Implementasi -->
        <PlanningSection :implementasi="screening.implementasiRawat" />
      </div>

      <!-- State: Data Kosong -->
      <div v-else class="card bg-base-100 border border-base-300 p-8 text-center rounded-3xl space-y-2">
        <div class="w-10 h-10 bg-base-200 text-base-content/40 rounded-2xl flex items-center justify-center mx-auto">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
        </div>
        <p class="text-xs font-bold text-base-content/80">Data Rekam Medis Kosong</p>
        <p class="text-[11px] text-base-content/50 max-w-xs mx-auto">
          Belum ada rincian pemeriksaan SOAP atau data belum dipublikasikan untuk pendaftaran ini.
        </p>
      </div>

    </main>
  </AppLayout>
</template>
