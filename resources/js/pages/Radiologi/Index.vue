<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  listRiwayat: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <Head title="Daftar Pemeriksaan Radiologi" />

  <AppLayout>
    <main class="p-4 sm:p-5 max-w-md mx-auto space-y-4 pb-12">
      <!-- Header Modern -->
      <div class="flex items-center gap-4 bg-gradient-to-r from-blue-600/10 via-base-100 to-base-100 p-4 rounded-3xl border border-base-300 shadow-xs">
        <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-md shadow-blue-500/20 shrink-0">
          🩻
        </div>
        <div>
          <h2 class="text-sm font-black text-base-content tracking-wide">Riwayat Radiologi</h2>
          <p class="text-[11px] text-base-content/60 font-medium">Daftar foto X-Ray, USG, & bacaan ekspertise</p>
        </div>
      </div>

      <!-- State: Kosong -->
      <div v-if="!listRiwayat || listRiwayat.length === 0" class="card bg-base-100 border border-base-300 p-10 text-center rounded-3xl space-y-3 shadow-xs">
        <div class="w-16 h-16 mx-auto bg-base-200 rounded-full flex items-center justify-center text-2xl">
          🖼️
        </div>
        <div class="space-y-1">
          <h3 class="text-xs font-bold text-base-content">Belum Ada Riwayat Radiologi</h3>
          <p class="text-[11px] text-base-content/50 max-w-[220px] mx-auto">
            Pemeriksaan rontgen, USG, atau CT-Scan yang pernah dilakukan akan muncul di sini.
          </p>
        </div>
      </div>

      <!-- State: List Riwayat Radiologi -->
      <div v-else class="space-y-3">
        <Link
          v-for="item in listRiwayat"
          :key="item.id_pendaftaran"
          :href="`/rekam-medis/radiologi/${item.id_pendaftaran}`"
          class="relative flex items-center justify-between p-4 bg-base-100 hover:bg-blue-500/[0.02] border border-base-300 hover:border-blue-500/40 rounded-3xl transition-all duration-300 shadow-xs hover:shadow-md group overflow-hidden"
        >
          <!-- Aksen Garis Kiri saat Hover -->
          <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>

          <div class="space-y-1.5 pl-1">
            <!-- ID Pendaftaran & Badge Foto -->
            <div class="flex items-center gap-2">
              <span class="px-2 py-0.5 bg-base-200 text-base-content/60 rounded-md text-[9px] font-mono font-semibold">
                ID: {{ item.id_pendaftaran }}
              </span>
              <span v-if="item.jumlah_foto" class="px-2 py-0.5 bg-blue-500/10 text-blue-600 rounded-md text-[9px] font-bold">
                {{ item.jumlah_foto }} Foto
              </span>
            </div>

            <!-- Tanggal Pemeriksaan -->
            <h4 class="text-xs font-bold text-base-content group-hover:text-blue-600 transition-colors flex items-center gap-1.5">
              <span>📅</span> {{ item.tgl_pemeriksaan || item.tanggal || item.tgl_registrasi || 'Tanggal tidak tersedia' }}
            </h4>

            <!-- Jenis Pemeriksaan / Poli & Dokter -->
            <p class="text-[11px] text-base-content/60 flex items-center gap-1.5">
              <span>🏥</span> <span class="font-medium text-base-content/80">{{ item.nama_poli || item.jenis_pemeriksaan || 'Radiologi' }}</span>
              <template v-if="item.dokter">
                <span class="text-base-content/30">•</span>
                <span>👨‍⚕️</span> <span class="truncate max-w-[130px]">{{ item.dokter }}</span>
              </template>
            </p>
          </div>

          <!-- Tombol Panah Aksi -->
          <div class="flex items-center justify-center w-8 h-8 rounded-full bg-base-200 text-base-content/60 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </Link>
      </div>
    </main>
  </AppLayout>
</template>
