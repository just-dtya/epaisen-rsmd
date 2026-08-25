<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  jadwal: {
    type: [Object, Array],
    default: () => ({})
  },
  selectedDate: {
    type: String,
    default: () => new Date().toISOString().split('T')[0]
  }
})

const localSelectedDate = ref(props.selectedDate)
const searchQuery = ref('')

// Inisialisasi activeDayId dari tanggal yang dipilih (1: Senin s/d 7: Minggu)
const getDayIdFromDate = (dateStr) => {
  const d = new Date(dateStr + 'T00:00:00')
  const dayIndex = d.getDay()
  return dayIndex === 0 ? 7 : dayIndex
}

const activeDayId = ref(getDayIdFromDate(props.selectedDate))

// Ekstraksi data dari props Inertia
const rawData = computed(() => props.jadwal || {})
const daftarHari = computed(() => rawData.value?.data?.daftar_hari || [])
const metaInfo = computed(() => rawData.value?.meta || {})

// Filter list dokter & poliklinik
const jadwalFiltered = computed(() => {
  if (!rawData.value?.data?.jadwal_mingguan) return []

  const listHari = rawData.value.data.jadwal_mingguan[String(activeDayId.value)] || []

  if (!searchQuery.value.trim()) return listHari

  const q = searchQuery.value.toLowerCase()
  return listHari.filter(item =>
    (item.nama_dokter && item.nama_dokter.toLowerCase().includes(q)) ||
    (item.deskripsi_poli && item.deskripsi_poli.toLowerCase().includes(q))
  )
})

// Handler perubahan Datepicker (Navigasi via Inertia)
const onDateChange = () => {
  activeDayId.value = getDayIdFromDate(localSelectedDate.value)
  router.get('/jadwal-dokter', { tanggal: localSelectedDate.value }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

// Handler klik Tab Hari
const onDayTabClick = (targetIdHari) => {
  if (activeDayId.value === targetIdHari) return

  activeDayId.value = targetIdHari

  const current = new Date(localSelectedDate.value + 'T00:00:00')
  const currentDayIndex = current.getDay() === 0 ? 7 : current.getDay()

  const diffDays = targetIdHari - currentDayIndex
  current.setDate(current.getDate() + diffDays)

  const year = current.getFullYear()
  const month = String(current.getMonth() + 1).padStart(2, '0')
  const day = String(current.getDate()).padStart(2, '0')

  localSelectedDate.value = `${year}-${month}-${day}`

  router.get('/jadwal-dokter', { tanggal: localSelectedDate.value }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const refreshData = () => {
  router.get('/jadwal-dokter', { tanggal: localSelectedDate.value }, {
    preserveScroll: true,
    replace: true
  })
}

const formatJam = (jam) => {
  if (!jam) return '-'
  return jam.substring(0, 5)
}
</script>

<template>
  <Head title="Jadwal Praktik Dokter" />

  <AppLayout>
    <main class="p-4 space-y-3.5 max-w-md mx-auto w-full select-none">

      <!-- 1. Header Card Halaman -->
      <div class="card bg-base-100 border border-base-300 shadow-2xs p-4 rounded-3xl">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-base font-black text-base-content tracking-tight">Jadwal Praktik Dokter</h2>
              <span v-if="metaInfo.hari" class="badge badge-primary badge-xs py-2 px-2 font-bold uppercase">
                {{ metaInfo.hari }}
              </span>
            </div>
            <p class="text-xs text-base-content/60">Informasi jam praktik & kehadiran dokter</p>
          </div>

          <button
            @click="refreshData"
            class="btn btn-sm btn-circle btn-ghost border border-base-300 bg-base-100 shadow-2xs"
            aria-label="Refresh Data"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </div>

      <!-- 2. Date Picker Selector -->
      <div class="card bg-base-100 border border-base-300 shadow-2xs p-3 rounded-2xl">
        <div class="flex items-center justify-between gap-2">
          <label for="pilihTanggal" class="text-xs font-bold text-base-content/80 flex items-center gap-1.5 shrink-0">
            <span>📅</span> Tanggal Periksa:
          </label>
          <input
            id="pilihTanggal"
            v-model="localSelectedDate"
            @change="onDateChange"
            type="date"
            class="input input-bordered input-xs rounded-xl text-xs bg-base-200/60 font-mono focus:input-primary"
          />
        </div>
      </div>

      <!-- 3. Tab Hari Interaktif -->
      <div v-if="daftarHari.length" class="flex gap-1.5 overflow-x-auto pb-1 no-scrollbar">
        <button
          v-for="hari in daftarHari"
          :key="hari.id_hari"
          @click="onDayTabClick(hari.id_hari)"
          class="btn btn-xs rounded-xl px-3 shrink-0 font-bold transition-all duration-150"
          :class="activeDayId === hari.id_hari ? 'btn-primary text-white shadow-2xs' : 'btn-ghost bg-base-100 border border-base-300 text-base-content/70'"
        >
          {{ hari.nama_hari }}
        </button>
      </div>

      <!-- Main Content List -->
      <div class="space-y-3">

        <!-- Search Input -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari poli atau nama dokter..."
            class="input input-sm input-bordered w-full bg-base-100 pl-9 rounded-2xl text-xs font-medium"
          />
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="btn btn-ghost btn-xs btn-circle absolute right-2 top-1 text-base-content/50"
          >
            ✕
          </button>
        </div>

        <!-- Empty State -->
        <div v-if="jadwalFiltered.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center text-xs text-base-content/60 rounded-2xl">
          Tidak ada jadwal praktik dokter yang tersedia.
        </div>

        <!-- Doctor Cards -->
        <div v-else class="space-y-2.5">
          <div
            v-for="(jadwalItem, idx) in jadwalFiltered"
            :key="idx"
            class="card bg-base-100 rounded-3xl border border-base-300 shadow-2xs p-4 space-y-3 transition-all"
            :class="jadwalItem.tgl_libur ? 'bg-base-200/40 border-dashed opacity-80' : ''"
          >
            <!-- Poli Badge & Status -->
            <div class="flex items-start justify-between gap-2.5">
              <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-primary/10 text-primary border border-primary/20 text-xs font-bold leading-relaxed break-words flex-1">
                <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                <span>{{ jadwalItem.deskripsi_poli }}</span>
              </div>

              <div class="shrink-0">
                <span
                  v-if="!jadwalItem.tgl_libur"
                  class="badge badge-success badge-sm text-[10px] font-bold text-white gap-1"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                  Praktik
                </span>
                <span
                  v-else
                  class="badge badge-error badge-sm text-[10px] font-bold text-white gap-1"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                  Libur
                </span>
              </div>
            </div>

            <!-- Keterangan Libur (Jika Ada) -->
            <div
              v-if="jadwalItem.tgl_libur"
              class="p-2.5 rounded-2xl bg-error/10 border border-error/20 text-error flex items-start gap-2 text-xs"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="leading-tight">
                <span class="font-bold block text-[11px] mb-0.5">Keterangan Tidak Praktik:</span>
                <span class="text-xs break-words">{{ jadwalItem.ket_libur || 'Izin / Cuti Praktik' }}</span>
              </div>
            </div>

            <!-- Dokter & Jam -->
            <div class="space-y-1.5 pt-0.5">
              <div class="font-black text-sm text-base-content flex items-center gap-2 leading-snug">
                <div class="avatar placeholder shrink-0">
                  <div class="bg-primary/10 text-primary w-8 h-8 rounded-xl text-xs flex items-center justify-center border border-primary/20">
                    👨‍⚕️
                  </div>
                </div>
                <span class="break-words flex-1">{{ jadwalItem.nama_dokter }}</span>
              </div>

              <div class="flex items-center gap-2 text-xs text-base-content/70 pl-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-base-content/50 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Jam Praktik: <strong class="text-base-content font-mono font-bold">{{ formatJam(jadwalItem.jam_mulai_praktik) }} - {{ formatJam(jadwalItem.jam_selesai_praktik) }}</strong> WIB</span>
              </div>
            </div>

            <!-- Pendaftaran Mandiri Action -->
            <div class="pt-2.5 border-t border-base-200/80 flex items-center justify-between gap-2">
              <span class="text-[10px] text-base-content/50 font-medium">Layanan Pasien</span>

              <a
                v-if="!jadwalItem.tgl_libur && jadwalItem.link_daftar_online"
                :href="jadwalItem.link_daftar_online"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-xs btn-primary gap-1.5 rounded-xl shadow-2xs text-white"
              >
                <span>Daftar Online</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </a>
              <span v-else class="badge badge-ghost badge-sm text-[10px] font-semibold text-base-content/40">
                Pendaftaran Tidak Tersedia
              </span>
            </div>

          </div>
        </div>

      </div>

    </main>
  </AppLayout>
</template>
