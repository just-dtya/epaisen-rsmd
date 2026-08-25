<script setup>
import { ref, computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import QuickMenuGrid from '@/components/QuickMenuGrid.vue'
import BeritaSection from '@/components/BeritaSection.vue'

const props = defineProps({
  patient: {
    type: Object,
    default: null
  },
  berita: {
    type: Array,
    default: () => []
  }
})

const page = usePage()
const showDetailModal = ref(false)

// Prioritaskan props.patient, fallback ke shared auth
const currentPatient = computed(() => {
  return props.patient || page.props.auth?.patient || page.props.auth?.user || {}
})

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour >= 4 && hour < 11) return 'Selamat Pagi'
  if (hour >= 11 && hour < 15) return 'Selamat Siang'
  if (hour >= 15 && hour < 18) return 'Selamat Sore'
  return 'Selamat Malam'
})

const formattedToday = computed(() => {
  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  }).format(new Date())
})
</script>

<template>
  <Head title="Beranda ePasien" />

  <AppLayout>
    <main class="p-4 space-y-4 max-w-md mx-auto w-full select-none pb-24">

      <!-- 1. Card Identitas Digital Pasien (e-Patient Card) -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary/15 via-base-100 to-base-100 border border-primary/20 shadow-md p-5 space-y-4">
        <div class="absolute -right-8 -bottom-8 w-36 h-36 rounded-full bg-primary/10 blur-xl pointer-events-none"></div>

        <!-- Top Header Card -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-base-100 p-1 border border-base-300 shadow-2xs flex items-center justify-center">
              <img src="/icon_rsmd.png" alt="Logo RSMD" class="w-full h-full object-contain" />
            </div>
            <div>
              <h1 class="text-xs font-black tracking-tight leading-none text-base-content">
                KARTU PASIEN DIGITAL
              </h1>
              <p class="text-[9px] text-base-content/50 font-semibold tracking-wider uppercase mt-0.5">
                RSMD Soepardjo Roestam
              </p>
            </div>
          </div>

          <span class="badge badge-primary badge-xs py-2 px-2.5 font-bold uppercase tracking-wider">
            {{ currentPatient.no_rkm_medis ? 'Terdaftar' : 'Pasien Baru' }}
          </span>
        </div>

        <!-- Detail Identitas Pasien -->
        <div class="space-y-2 pt-1 border-t border-base-200">
          <div>
            <p class="text-[10px] text-base-content/50 font-bold uppercase tracking-wider">Nama Pasien</p>
            <h2 class="text-base font-extrabold text-base-content tracking-tight leading-snug">
              {{ currentPatient.nama || 'Nama Pasien' }}
            </h2>
          </div>

          <div class="grid grid-cols-2 gap-2 pt-1">
            <div class="bg-base-200/50 p-2.5 rounded-2xl border border-base-300/60">
              <span class="text-[9px] text-base-content/50 font-bold uppercase tracking-wider block">No. Rekam Medis</span>
              <span class="text-xs font-black font-mono text-primary tracking-wide">
                {{ currentPatient.no_rkm_medis || 'Menunggu Loket' }}
              </span>
            </div>

            <div class="bg-base-200/50 p-2.5 rounded-2xl border border-base-300/60">
              <span class="text-[9px] text-base-content/50 font-bold uppercase tracking-wider block">NIK KTP</span>
              <span class="text-xs font-bold font-mono text-base-content tracking-tight truncate block">
                {{ currentPatient.nik || '-' }}
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between text-[11px] text-base-content/70 px-1 pt-1">
            <span>📅 {{ currentPatient.tgl_lahir || '-' }} ({{ currentPatient.jk === 'L' ? 'Laki-Laki' : 'Perempuan' }})</span>
            <button
              @click="showDetailModal = true"
              class="text-primary font-bold hover:underline inline-flex items-center gap-0.5 text-xs"
            >
              Lihat Detail
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>

      </div>

      <!-- 2. Banner Sapaan -->
      <div class="flex items-center justify-between px-2 text-xs">
        <span class="text-base-content/60 font-semibold">
          {{ greeting }}, <strong>{{ currentPatient.nama ? currentPatient.nama.split(' ')[0] : 'Pasien' }}</strong>
        </span>
        <span class="badge badge-ghost border border-base-300 text-[10px] font-bold text-base-content/70">
          {{ formattedToday }}
        </span>
      </div>

      <!-- 3. Komponen Menu Cepat Pelayanan -->
      <QuickMenuGrid />

      <!-- 4. Section Berita & Edukasi Kesehatan -->
      <BeritaSection :berita="berita" />

      <!-- 5. Modal Detail Biodata Pasien -->
      <dialog class="modal" :class="{ 'modal-open': showDetailModal }">
        <div class="modal-box rounded-3xl border border-base-300 max-w-sm p-5 space-y-4">
          <div class="flex items-center justify-between border-b border-base-200 pb-3">
            <h3 class="font-black text-sm text-base-content">Biodata Lengkap Pasien</h3>
            <button @click="showDetailModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
          </div>

          <div class="space-y-2.5 text-xs">
            <div class="flex justify-between border-b border-base-200/60 pb-1.5">
              <span class="text-base-content/50">No. Rekam Medis</span>
              <span class="font-bold font-mono text-primary">{{ currentPatient.no_rkm_medis || '-' }}</span>
            </div>
            <div class="flex justify-between border-b border-base-200/60 pb-1.5">
              <span class="text-base-content/50">NIK KTP</span>
              <span class="font-bold font-mono">{{ currentPatient.nik || '-' }}</span>
            </div>
            <div class="flex justify-between border-b border-base-200/60 pb-1.5">
              <span class="text-base-content/50">Tempat, Tgl Lahir</span>
              <span class="font-bold text-right">{{ currentPatient.tmp_lahir }}, {{ currentPatient.tgl_lahir }}</span>
            </div>
            <div class="flex justify-between border-b border-base-200/60 pb-1.5">
              <span class="text-base-content/50">Jenis Kelamin</span>
              <span class="font-bold">{{ currentPatient.jk === 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
            </div>
            <div class="flex justify-between border-b border-base-200/60 pb-1.5">
              <span class="text-base-content/50">No. WhatsApp / HP</span>
              <span class="font-bold font-mono">{{ currentPatient.no_tlp || '-' }}</span>
            </div>
            <div class="flex justify-between border-b border-base-200/60 pb-1.5">
              <span class="text-base-content/50">Nama Orang Tua / Wali</span>
              <span class="font-bold">{{ currentPatient.nm_ibu || '-' }}</span>
            </div>
            <div class="space-y-1 pt-1">
              <span class="text-base-content/50 block">Alamat Domisili:</span>
              <p class="font-medium bg-base-200/50 p-2 rounded-xl text-[11px] leading-relaxed">
                {{ currentPatient.alamat || '-' }}
              </p>
            </div>
          </div>

          <div class="modal-action pt-2">
            <button
              @click="showDetailModal = false"
              class="btn btn-sm btn-primary btn-block rounded-xl text-white font-bold"
            >
              Tutup
            </button>
          </div>
        </div>
      </dialog>

    </main>
  </AppLayout>
</template>
