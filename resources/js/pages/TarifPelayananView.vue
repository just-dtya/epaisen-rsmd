<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  tarif: {
    type: Array,
    default: () => []
  }
})

const searchQuery = ref('')
const selectedCategory = ref('ALL')
const currentPage = ref(1)
const perPage = ref(12)

// Format Mata Uang Rupiah
const formatRupiah = (val) => {
  const num = Number(val) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(num)
}

// Daftar kategori unik beserta jumlah itemnya
const categoryStats = computed(() => {
  const map = {}
  props.tarif.forEach(item => {
    const cat = item.nama_kategori_tarif || 'Lainnya'
    map[cat] = (map[cat] || 0) + 1
  })
  return map
})

const categories = computed(() => Object.keys(categoryStats.value))

// Filter data pencarian & kategori
const filteredTarif = computed(() => {
  return props.tarif.filter(item => {
    const matchCategory = selectedCategory.value === 'ALL' || item.nama_kategori_tarif === selectedCategory.value

    const q = searchQuery.value.toLowerCase().trim()
    const matchSearch = !q ||
      item.nama_tarif_pelayanan?.toLowerCase().includes(q) ||
      item.KodeTarif?.toLowerCase().includes(q) ||
      item.nama_kategori_tarif?.toLowerCase().includes(q)

    return matchCategory && matchSearch
  })
})

// Pagination
const totalPages = computed(() => Math.ceil(filteredTarif.value.length / perPage.value) || 1)
const paginatedTarif = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredTarif.value.slice(start, start + perPage.value)
})

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 180, behavior: 'smooth' })
  }
}
</script>

<template>
  <Head title="Tarif Pelayanan Medis - RSMD" />

  <AppLayout>
    <main class="p-4 space-y-4 max-w-md mx-auto w-full select-none pb-24">

      <!-- Hero Banner Card -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-teal-700 to-emerald-800 text-white p-5 shadow-lg shadow-primary/20 space-y-3">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between relative z-10">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-[10px] font-bold tracking-wide uppercase">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Pergub Jateng No. 35 / 2024
          </div>
          <span class="text-[11px] font-mono font-bold bg-black/20 px-2.5 py-1 rounded-xl">
            {{ props.tarif.length }} Tindakan
          </span>
        </div>

        <div class="relative z-10 space-y-1">
          <h1 class="text-xl font-black tracking-tight leading-tight">
            Tarif Pelayanan Medis
          </h1>
          <p class="text-xs text-white/80 leading-relaxed">
            Transparansi tarif retribusi pemeriksaan, tindakan poliklinik, dan penunjang medis RSMD.
          </p>
        </div>

        <div class="pt-2 border-t border-white/15 flex items-center gap-1.5 text-[10px] text-white/70">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="truncate">Belum termasuk biaya Farmasi & Bahan Habis Pakai (BHP).</span>
        </div>
      </div>

      <!-- Search & Filter Card -->
      <div class="card bg-base-100 border border-base-300 shadow-2xs p-3.5 rounded-3xl space-y-3">

        <!-- Input Search Bar -->
        <div class="relative">
          <input
            v-model="searchQuery"
            @input="currentPage = 1"
            type="text"
            placeholder="Cari nama tindakan, poli, kode tarif..."
            class="input input-sm w-full bg-base-200/70 focus:bg-base-100 rounded-2xl pl-9 pr-8 text-xs focus:outline-primary border-base-300 transition-all"
          />
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button
            v-if="searchQuery"
            @click="searchQuery = ''; currentPage = 1"
            class="btn btn-xs btn-circle btn-ghost absolute right-1.5 top-1.5 text-base-content/40"
          >
            ✕
          </button>
        </div>

        <!-- Filter Chips Kategori -->
        <div class="flex gap-1.5 overflow-x-auto no-scrollbar pt-0.5">
          <button
            @click="selectedCategory = 'ALL'; currentPage = 1"
            class="btn btn-xs rounded-xl px-3 whitespace-nowrap font-bold shrink-0 transition-all"
            :class="selectedCategory === 'ALL' ? 'btn-primary text-white shadow-2xs' : 'btn-ghost bg-base-200/70 text-base-content/70'"
          >
            Semua ({{ props.tarif.length }})
          </button>
          <button
            v-for="cat in categories"
            :key="cat"
            @click="selectedCategory = cat; currentPage = 1"
            class="btn btn-xs rounded-xl px-3 whitespace-nowrap font-bold shrink-0 transition-all flex items-center gap-1.5"
            :class="selectedCategory === cat ? 'btn-primary text-white shadow-2xs' : 'btn-ghost bg-base-200/70 text-base-content/70'"
          >
            <span>{{ cat }}</span>
            <span
              class="badge badge-xs px-1.5 py-0.5 rounded-md font-mono"
              :class="selectedCategory === cat ? 'bg-white/20 text-white' : 'bg-base-300 text-base-content/60'"
            >
              {{ categoryStats[cat] }}
            </span>
          </button>
        </div>
      </div>

      <!-- Counter & Info Pencarian -->
      <div class="flex items-center justify-between px-2 text-[11px] text-base-content/60 font-semibold">
        <span>Menampilkan <strong>{{ paginatedTarif.length }}</strong> dari <strong>{{ filteredTarif.length }}</strong> layanan</span>
        <span>Halaman {{ currentPage }}/{{ totalPages }}</span>
      </div>

      <!-- Empty State -->
      <div v-if="filteredTarif.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center rounded-3xl space-y-3">
        <div class="w-14 h-14 bg-base-200 rounded-3xl flex items-center justify-center mx-auto text-2xl">
          🔍
        </div>
        <div>
          <h3 class="text-xs font-bold text-base-content">Layanan tidak ditemukan</h3>
          <p class="text-[11px] text-base-content/50 mt-0.5">Coba gunakan kata kunci pencarian yang lain.</p>
        </div>
        <button
          @click="searchQuery = ''; selectedCategory = 'ALL'"
          class="btn btn-xs btn-primary rounded-xl text-white font-bold mx-auto"
        >
          Reset Filter
        </button>
      </div>

      <!-- List Item Tarif -->
      <div v-else class="space-y-3">
        <div
          v-for="(item, idx) in paginatedTarif"
          :key="item.KodeTarif || idx"
          class="card bg-base-100 border border-base-300 shadow-2xs hover:border-primary/40 rounded-3xl p-4 space-y-3 transition-all duration-150 group"
        >
          <!-- Kategori & Kode -->
          <div class="flex items-start justify-between gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-base-200 text-base-content/70 text-[10px] font-bold truncate max-w-[210px]">
              <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
              <span class="truncate">{{ item.nama_kategori_tarif }}</span>
            </span>

            <span class="font-mono text-[10px] font-extrabold text-primary bg-primary/10 border border-primary/20 px-2 py-0.5 rounded-lg shrink-0">
              #{{ item.KodeTarif }}
            </span>
          </div>

          <!-- Nama Tindakan -->
          <h2 class="text-xs font-extrabold text-base-content tracking-tight leading-snug group-hover:text-primary transition-colors">
            {{ item.nama_tarif_pelayanan }}
          </h2>

          <!-- Satuan & Nominal Tarif -->
          <div class="bg-base-200/50 rounded-2xl p-3 border border-base-300/60 flex items-center justify-between">
            <div>
              <span class="text-[9px] text-base-content/50 font-bold uppercase tracking-wider block">Satuan</span>
              <span class="text-xs font-bold text-base-content/70 uppercase">
                {{ item.nm_satuan_tarif || 'Per Tindakan' }}
              </span>
            </div>

            <div class="text-right">
              <span class="text-[9px] text-base-content/50 font-bold uppercase tracking-wider block">Total Tarif</span>
              <span class="text-sm font-black font-mono text-primary tracking-tight">
                {{ formatRupiah(item.nominal_tarif) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Pagination Navigation -->
        <div v-if="totalPages > 1" class="flex justify-center items-center gap-2 pt-3">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="btn btn-xs btn-circle btn-ghost border border-base-300 disabled:opacity-30"
          >
            ←
          </button>
          <span class="text-xs font-bold font-mono px-3 py-1 bg-base-100 rounded-xl border border-base-300">
            {{ currentPage }} / {{ totalPages }}
          </span>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="btn btn-xs btn-circle btn-ghost border border-base-300 disabled:opacity-30"
          >
            →
          </button>
        </div>
      </div>

    </main>
  </AppLayout>
</template>
