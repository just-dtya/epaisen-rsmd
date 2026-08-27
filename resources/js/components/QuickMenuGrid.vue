<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import LayananModal from '../components/home/LayananModal.vue'

const showAllModal = ref(false)

// 1. Menu Utama di Grid Beranda
const primaryMenus = [
    {
        id: 'jadwal',
        label: 'Jadwal Dokter',
        href: '/jadwal-dokter',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        color: 'text-primary bg-primary/10 hover:bg-primary/20',
    },
    {
        id: 'liat_rsmd',
        label: 'LiatRSMD',
        href: '/rsmd',
        icon: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
        color: 'text-rose-500 bg-rose-500/10 hover:bg-rose-500/20',
        badge: 'Live'
    },
    {
        id: 'antrean',
        label: 'Antrean Poli',
        href: '/antrean',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        color: 'text-emerald-600 bg-emerald-500/10 hover:bg-emerald-500/20',
    },
    {
        id: 'bed_ranap',
        label: 'Bed Ranap',
        href: '/bed-monitoring',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        color: 'text-teal-600 bg-teal-500/10 hover:bg-teal-500/20',
    },
    {
        id: 'riwayat',
        label: 'Riwayat',
        href: '/riwayat',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        color: 'text-indigo-600 bg-indigo-500/10 hover:bg-indigo-500/20',
    },
    {
        id: 'tarif',
        label: 'Tarif Layanan',
        href: '/tarif',
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        color: 'text-amber-600 bg-amber-500/10 hover:bg-amber-500/20',
    },
    {
        id: 'berita',
        label: 'Kabar RSMD',
        href: '/berita',
        icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
        color: 'text-sky-600 bg-sky-500/10 hover:bg-sky-500/20',
    }
]

// 2. Daftar Lengkap Seluruh Layanan per Kategori
const allServiceCategories = [
    {
        title: 'Pelayanan Pasien & Rawat Jalan',
        items: [
            {
                id: 'jadwal',
                label: 'Jadwal Praktik Dokter',
                desc: 'Jadwal poliklinik & dokter spesialis',
                href: '/jadwal-dokter',
                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                color: 'text-primary bg-primary/10'
            },
            {
                id: 'pendaftaran',
                label: 'Pendaftaran Pasien',
                desc: 'Registrasi online pasien BPJS & Umum',
                href: '/pendaftaran',
                icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                color: 'text-emerald-600 bg-emerald-500/10'
            },
            {
                id: 'antrean',
                label: 'Monitoring Antrean',
                desc: 'Pantau status panggilan loket & poli',
                href: '/antrean',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                color: 'text-teal-600 bg-teal-500/10'
            },
            {
                id: 'riwayat',
                label: 'Riwayat Kunjungan',
                desc: 'Catatan rekam medis & riwayat berobat',
                href: '/riwayat',
                icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                color: 'text-indigo-600 bg-indigo-500/10'
            },
        ]
    },
    {
        title: 'Fasilitas & Informasi Rumah Sakit',
        items: [
            {
                id: 'liat_rsmd',
                label: 'LiatRSMD (CCTV Live)',
                desc: 'Pantau situasi ruang tunggu & parkiran',
                href: '/rsmd',
                icon: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                color: 'text-rose-500 bg-rose-500/10',
                badge: 'Live'
            },
            {
                id: 'bed_ranap',
                label: 'Ketersediaan Bed Ranap',
                desc: 'Status kapasitas kamar rawat inap',
                href: '/bed-monitoring',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                color: 'text-teal-600 bg-teal-500/10'
            },
            {
                id: 'tarif',
                label: 'Tarif Pelayanan Medis',
                desc: 'Transparansi biaya retribusi tindakan',
                href: '/tarif',
                icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                color: 'text-amber-600 bg-amber-500/10'
            },
            {
                id: 'berita',
                label: 'Kabar & Edukasi RSMD',
                desc: 'Warta terkini PPID & artikel kesehatan',
                href: '/berita',
                icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
                color: 'text-sky-600 bg-sky-500/10'
            },
            {
                id: 'farmasi',
                label: 'Antrean Farmasi',
                desc: 'Pengambilan obat & resep racikan',
                href: '/farmasi',
                icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                color: 'text-purple-600 bg-purple-500/10'
            }
        ]
    }
]
</script>

<template>
    <div class="space-y-3">
        <!-- Section Header Minimalis -->
        <div class="flex items-center justify-between px-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">
                Menu Layanan
            </span>
            <button
                @click="showAllModal = true"
                class="text-[11px] font-bold text-primary hover:underline inline-flex items-center gap-0.5 active:scale-95 transition-transform"
            >
                Semua Layanan →
            </button>
        </div>

        <!-- Grid 4-Kolom Minimalis Modern -->
        <div class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4">
            <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                <!-- 7 Item Menu Utama -->
                <Link
                    v-for="menu in primaryMenus"
                    :key="menu.id"
                    :href="menu.href"
                    class="flex flex-col items-center text-center gap-1.5 group active:scale-90 transition-transform select-none"
                >
                    <div class="relative">
                        <div
                            class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-200 group-hover:scale-105 shadow-2xs"
                            :class="menu.color"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menu.icon" />
                            </svg>
                        </div>

                        <span
                            v-if="menu.badge"
                            class="absolute -top-1 -right-1 px-1.5 py-0.2 bg-rose-500 text-white text-[8px] font-extrabold rounded-full ring-2 ring-base-100 animate-pulse"
                        >
                            {{ menu.badge }}
                        </span>
                    </div>

                    <span class="text-[11px] font-bold text-base-content leading-tight line-clamp-1 group-hover:text-primary transition-colors">
                        {{ menu.label }}
                    </span>
                </Link>

                <!-- Tombol ke-8: Buka Modal Semua Layanan -->
                <button
                    @click="showAllModal = true"
                    class="flex flex-col items-center text-center gap-1.5 group active:scale-90 transition-transform select-none"
                >
                    <div class="w-12 h-12 rounded-2xl bg-base-200 text-base-content/70 hover:bg-base-300 flex items-center justify-center transition-all duration-200 group-hover:scale-105 shadow-2xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>

                    <span class="text-[11px] font-bold text-base-content/70 leading-tight">
                        Lainnya
                    </span>
                </button>
            </div>
        </div>

        <!-- Modal Component -->
        <LayananModal
            :show="showAllModal"
            :categories="allServiceCategories"
            @close="showAllModal = false"
        />
    </div>
</template>
