<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    user: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['close', 'logout'])
const page = usePage()

// Resolusi sumber data pasien global & lokal props
const currentUser = computed(() => {
    return props.user
        || page.props.patient
        || page.props.auth?.patient
        || page.props.auth?.user
        || null
})

// Resolusi nama lengkap pasien
const displayName = computed(() => {
    if (!currentUser.value) return 'Pasien / Pengunjung'
    return currentUser.value.nama
        || currentUser.value.name
        || currentUser.value.nama_pasien
        || 'Pasien RSMD'
})

// Resolusi nomor rekam medis
const noRekamMedis = computed(() => {
    if (!currentUser.value) return '-'
    return currentUser.value.no_rkm_medis
        || currentUser.value.no_rm
        || currentUser.value.id_pasien_simrs
        || '-'
})

// Inisial avatar profil
const initials = computed(() => {
    if (!displayName.value || displayName.value === 'Pasien / Pengunjung') return 'PS'
    const parts = displayName.value.trim().split(' ').filter(Boolean)
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return displayName.value.substring(0, 2).toUpperCase()
})

const handleLogout = () => {
    emit('close')
    router.post('/logout')
}

// === GESTURE HANDLING: DRAG TO DISMISS ===
const asideRef = ref(null)
const startX = ref(0)
const currentX = ref(0)
const translateX = ref(0)
const isDragging = ref(false)
const DISMISS_THRESHOLD = 70

const onTouchStart = (e) => {
    startX.value = e.touches[0].clientX
    isDragging.value = true
}

const onTouchMove = (e) => {
    if (!isDragging.value) return
    currentX.value = e.touches[0].clientX
    const delta = currentX.value - startX.value

    if (delta < 0) {
        translateX.value = delta
    }
}

const onTouchEnd = () => {
    if (!isDragging.value) return
    isDragging.value = false

    if (Math.abs(translateX.value) > DISMISS_THRESHOLD) {
        translateX.value = -300
        setTimeout(() => {
            emit('close')
            translateX.value = 0
        }, 150)
    } else {
        translateX.value = 0
    }
}

const menuSections = [
    {
        title: 'Utama & Pelayanan',
        items: [
            {
                id: 'home',
                label: 'Beranda Utama',
                href: '/dashboard',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
            },
            {
                id: 'riwayat',
                label: 'Riwayat Kunjungan',
                href: '/riwayat',
                icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
            },
            {
                id: 'lab',
                label: 'Hasil Laboratorium',
                href: '/rekam-medis/lab',
                icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'
            },
            {
                id: 'radiologi',
                label: 'Riwayat Radiologi',
                href: '/rekam-medis/radiologi',
                icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'
            },
            {
                id: 'liat_rsmd',
                label: 'LiatRSMD (CCTV & Antrean)',
                href: '/rsmd',
                icon: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                badge: 'Live'
            },
            {
                id: 'bed_monitoring',
                label: 'Ketersediaan Bed Ranap',
                href: '/bed-monitoring',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
            },
            {
                id: 'berita',
                label: 'Berita & Informasi',
                href: '/berita',
                icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'
            },
            {
                id: 'jadwal',
                label: 'Jadwal Praktik Dokter',
                href: '/jadwal-dokter',
                icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
            },
            {
                id: 'tarif',
                label: 'Tarif Pelayanan Medis',
                href: '/tarif',
                icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
            }
        ]
    },
    {
        title: 'Layanan Mandiri',
        items: [
            {
                id: 'pendaftaran',
                label: 'Pendaftaran Pasien',
                href: '/pendaftaran',
                icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
            },
            {
                id: 'antrean',
                label: 'Monitoring Antrean',
                href: '/antrean',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
            }
        ]
    }
]

const isRouteActive = (href) => {
    return page.url === href || page.url.startsWith(href + '/') || page.url.startsWith(href + '?')
}
</script>

<template>
    <Teleport to="body">
        <div class="drawer drawer-start z-50">
            <input id="app-drawer-toggle" type="checkbox" class="drawer-toggle" :checked="show"
                @change="!$event.target.checked && $emit('close')" />

            <div class="drawer-side">
                <label for="app-drawer-toggle" aria-label="close sidebar"
                    class="drawer-overlay bg-neutral/40 backdrop-blur-xs" @click="$emit('close')"></label>

                <aside ref="asideRef" @touchstart="onTouchStart" @touchmove="onTouchMove" @touchend="onTouchEnd"
                    class="bg-base-100 text-base-content min-h-full w-72 sm:w-80 p-4 flex flex-col justify-between border-r border-base-300 shadow-2xl transition-transform touch-pan-y"
                    :class="{ 'duration-200 ease-out': !isDragging }"
                    :style="{ transform: `translateX(${translateX}px)` }">
                    <div class="space-y-4">

                        <!-- Header Sidebar -->
                        <div class="flex items-center justify-between pb-3 border-b border-base-200">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-9 h-9 rounded-2xl bg-base-200 p-1.5 border border-base-300 shadow-2xs flex items-center justify-center">
                                    <img src="/icon_rsmd.png" alt="Logo RSMD" class="w-full h-full object-contain" />
                                </div>
                                <div>
                                    <h2 class="text-sm font-black tracking-tight leading-none text-base-content">
                                        RSMD<span class="text-primary font-bold">Mobile</span>
                                    </h2>
                                    <p class="text-[10px] text-base-content/50 font-medium mt-0.5">
                                        Sistem Rawat Jalan
                                    </p>
                                </div>
                            </div>

                            <button @click="$emit('close')" class="btn btn-sm btn-circle btn-ghost text-base-content/60"
                                aria-label="Tutup Menu">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- User Profile Box -->
                        <div class="p-3 bg-base-200/50 rounded-2xl border border-base-300/60 flex items-center gap-2.5">
                            <div class="avatar placeholder shrink-0">
                                <div
                                    class="bg-primary/10 text-primary w-10 h-10 rounded-xl font-bold flex items-center justify-center text-xs">
                                    {{ initials }}
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-base-content truncate">{{ displayName }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span
                                        class="badge badge-primary/15 text-primary border-0 text-[9px] font-bold px-1.5 py-0 h-4 uppercase">
                                        {{ noRekamMedis !== '-' ? 'Pasien' : 'Pengunjung' }}
                                    </span>
                                    <span class="text-[10px] text-base-content/50 font-mono truncate font-semibold">
                                        RM: {{ noRekamMedis }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Navigation List -->
                        <div class="space-y-3 overflow-y-auto max-h-[calc(100vh-280px)] no-scrollbar pr-1">
                            <div v-for="(section, sIdx) in menuSections" :key="sIdx" class="space-y-1">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-wider text-base-content/40 px-2 block">
                                    {{ section.title }}
                                </span>

                                <ul class="menu menu-sm p-0 gap-1">
                                    <li v-for="item in section.items" :key="item.id">
                                        <Link :href="item.href" @click="$emit('close')"
                                            class="flex items-center justify-between py-2.5 px-3 rounded-xl transition-all font-medium text-xs active:scale-[0.98]"
                                            :class="isRouteActive(item.href)
                                                ? 'bg-primary text-white font-bold shadow-2xs'
                                                : 'text-base-content/80 hover:bg-base-200'">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" :d="item.icon" />
                                                </svg>
                                                <span class="truncate">{{ item.label }}</span>
                                            </div>

                                            <span v-if="item.badge" class="badge badge-xs shrink-0 font-bold"
                                                :class="isRouteActive(item.href) ? 'badge-neutral text-white' : 'badge-error text-white'">
                                                {{ item.badge }}
                                            </span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Actions -->
                    <div class="pt-3 border-t border-base-200 space-y-2">
                        <button @click="handleLogout"
                            class="btn btn-sm btn-error btn-outline btn-block rounded-xl text-xs font-bold gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                        <p class="text-center text-[10px] text-base-content/40 font-medium">
                            ← Geser ke kiri untuk menutup
                        </p>
                    </div>

                </aside>
            </div>
        </div>
    </Teleport>
</template>
