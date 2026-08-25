<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const isActive = (path) => {
    if (path === '/dashboard') {
        return page.url === '/dashboard' || page.url === '/'
    }
    return page.url.startsWith(path)
}
</script>

<template>
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-base-100/95 backdrop-blur-md border-t border-base-300 shadow-lg">
        <div class="max-w-md mx-auto flex items-center justify-around h-16 px-4">

            <!-- 1. Beranda -->
            <Link href="/dashboard"
                class="flex flex-col items-center justify-center flex-1 h-full transition-all duration-150 active:scale-95"
                :class="isActive('/dashboard') ? 'text-primary font-bold' : 'text-base-content/60 hover:text-base-content'">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span v-if="isActive('/dashboard')"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-primary rounded-full"></span>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Beranda</span>
            </Link>

            <!-- 2. Jadwal Dokter -->
            <Link href="/jadwal-dokter"
                class="flex flex-col items-center justify-center flex-1 h-full transition-all duration-150 active:scale-95"
                :class="isActive('/jadwal-dokter') ? 'text-primary font-bold' : 'text-base-content/60 hover:text-base-content'">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span v-if="isActive('/jadwal-dokter')"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-primary rounded-full"></span>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Jadwal</span>
            </Link>

            <!-- 3. LiatRSMD (CCTV & Antrean) -->
            <Link href="/rsmd"
                class="flex flex-col items-center justify-center flex-1 h-full transition-all duration-150 active:scale-95"
                :class="isActive('/rsmd') ? 'text-primary font-bold' : 'text-base-content/60 hover:text-base-content'">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span class="absolute -top-1 -right-1.5 flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-error"></span>
                    </span>
                    <span v-if="isActive('/rsmd')"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-primary rounded-full"></span>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">LiatRSMD</span>
            </Link>
            <!-- Riwayat Kunjungan -->
            <Link href="/riwayat"
                class="flex flex-col items-center justify-center flex-1 h-full transition-all duration-150 active:scale-95"
                :class="isActive('/riwayat') ? 'text-primary font-bold' : 'text-base-content/60 hover:text-base-content'">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span v-if="isActive('/riwayat')"
                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-primary rounded-full"></span>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Riwayat</span>
            </Link>

        </div>
    </nav>
</template>
