<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  riwayat: {
    type: Array,
    default: () => []
  }
})

const filterStatus = ref('all')

const filteredRiwayat = computed(() => {
  if (filterStatus.value === 'selesai') {
    return props.riwayat.filter(item => item.sts_periksa === 1)
  }
  if (filterStatus.value === 'menunggu') {
    return props.riwayat.filter(item => item.sts_periksa !== 1)
  }
  return props.riwayat
})
</script>

<template>
  <Head title="Riwayat Kunjungan Pasien" />

  <AppLayout>
    <main class="p-4 space-y-3.5 max-w-md mx-auto w-full select-none pb-20">

      <!-- Header Card -->
      <div class="card bg-base-100 border border-base-300 shadow-2xs p-4 rounded-3xl">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-base font-black text-base-content tracking-tight">Riwayat Kunjungan</h2>
            <p class="text-xs text-base-content/60">Histori pendaftaran & pemeriksaan poli</p>
          </div>
          <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-base">
            📋
          </div>
        </div>

        <!-- Filter Tab -->
        <div class="flex gap-1.5 pt-3 border-t border-base-200 mt-3">
          <button
            @click="filterStatus = 'all'"
            class="btn btn-xs rounded-xl px-3 font-bold transition-all"
            :class="filterStatus === 'all' ? 'btn-primary text-white' : 'btn-ghost bg-base-200/60 text-base-content/70'"
          >
            Semua ({{ riwayat.length }})
          </button>
          <button
            @click="filterStatus = 'menunggu'"
            class="btn btn-xs rounded-xl px-3 font-bold transition-all"
            :class="filterStatus === 'menunggu' ? 'btn-primary text-white' : 'btn-ghost bg-base-200/60 text-base-content/70'"
          >
            Menunggu
          </button>
          <button
            @click="filterStatus = 'selesai'"
            class="btn btn-xs rounded-xl px-3 font-bold transition-all"
            :class="filterStatus === 'selesai' ? 'btn-primary text-white' : 'btn-ghost bg-base-200/60 text-base-content/70'"
          >
            Selesai
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredRiwayat.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center rounded-3xl space-y-2">
        <p class="text-3xl">📂</p>
        <p class="text-xs font-bold text-base-content/70">Belum ada riwayat kunjungan</p>
        <p class="text-[11px] text-base-content/50">Daftar pemeriksaan rawat jalan Anda akan tampil di sini.</p>
        <div class="pt-2">
          <Link href="/pendaftaran" class="btn btn-xs btn-primary rounded-xl text-white font-bold">
            Daftar Berobat
          </Link>
        </div>
      </div>

      <!-- List Riwayat Kunjungan Cards -->
      <div v-else class="space-y-3">
        <div
          v-for="(item, idx) in filteredRiwayat"
          :key="item.id_pendaftaran || idx"
          class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3"
        >
          <!-- 1. Header Kunjungan (Poli & Status Periksa) -->
          <div class="flex items-start justify-between gap-2">
            <div>
              <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-xl bg-primary/10 text-primary border border-primary/20 text-xs font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                <span>{{ item.deskripsi_poli }}</span>
              </div>
              <p class="text-[10px] text-base-content/50 mt-1 font-medium">{{ item.nama_instalasi }} • {{ item.deskripsi_ruangan }}</p>
            </div>

            <!-- Status Badge -->
            <span
              v-if="item.sts_periksa === 1"
              class="badge badge-success badge-sm text-[10px] font-bold text-white shrink-0"
            >
              Selesai Periksa
            </span>
            <span
              v-else-if="item.sts_presensi === 1"
              class="badge badge-info badge-sm text-[10px] font-bold text-white shrink-0 animate-pulse"
            >
              Sedang Mengantre
            </span>
            <span
              v-else
              class="badge badge-warning badge-sm text-[10px] font-bold text-white shrink-0"
            >
              Belum Presensi
            </span>
          </div>

          <!-- 2. Dokter & Jadwal Praktik -->
          <div class="flex items-center gap-2.5 pt-0.5">
            <div class="w-8 h-8 rounded-xl bg-base-200 text-base-content/70 flex items-center justify-center text-xs shrink-0 border border-base-300">
              👨‍⚕️
            </div>
            <div class="min-w-0 flex-1">
              <h4 class="text-xs font-bold text-base-content truncate">{{ item.nama_dokter }}</h4>
              <p class="text-[10px] text-base-content/60 font-medium">
                {{ item.nama_hari ? item.nama_hari + ', ' : '' }}{{ item.tgl_periksa }} ({{ item.jam_praktik }})
              </p>
            </div>
          </div>

          <!-- 3. Kotak Antrean & Penjamin -->
          <div class="bg-base-200/50 rounded-2xl p-3 border border-base-300/60 flex items-center justify-between">
            <div>
              <span class="text-[9px] text-base-content/50 font-bold uppercase tracking-wider block">No. Antrean Poli</span>
              <span class="text-base font-black font-mono text-primary">{{ item.no_antrian }}</span>
              <p class="text-[9px] text-base-content/40 font-mono mt-0.5">{{ item.id_pendaftaran }}</p>
            </div>

            <div class="text-right space-y-1">
              <span class="badge badge-ghost border border-base-300 text-[10px] font-bold text-base-content/70">
                {{ item.nama_penjamin }}
              </span>
              <p class="text-[10px] text-base-content/50">
                Layanan: <strong class="text-base-content">{{ item.nama_jenis_layanan }}</strong>
              </p>
            </div>
          </div>

          <!-- 4. Rincian Jam Pelayanan -->
          <div class="grid grid-cols-2 gap-2 text-[10px] text-base-content/60 pt-1 border-t border-base-200/80">
            <div>
              <span class="text-base-content/40 block">Jam Presensi:</span>
              <span class="font-bold text-base-content">{{ item.presensi_at ? item.presensi_at + ' WIB' : '-' }}</span>
            </div>
            <div class="text-right">
              <span class="text-base-content/40 block">Jam Dilayani:</span>
              <span class="font-bold text-base-content">{{ item.periksa_at ? item.periksa_at + ' WIB' : '-' }}</span>
            </div>
          </div>

        </div>
      </div>

    </main>
  </AppLayout>
</template>
