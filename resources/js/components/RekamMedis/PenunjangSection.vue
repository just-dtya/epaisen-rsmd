<script setup>
import { ref } from 'vue'

const props = defineProps({
    hasilLab: {
        type: [Array, Object],
        default: () => []
    },
    hasilRadiologi: {
        type: [Array, Object],
        default: () => []
    }
})

// State untuk Lightbox Gambar Radiologi
const activeImage = ref(null)

const openModal = (imgSrc) => {
    activeImage.value = imgSrc
}

const closeModal = () => {
    activeImage.value = null
}

const hasContent = (val) => {
    if (val === null || val === undefined || val === '' || val === false) return false
    if (Array.isArray(val)) return val.length > 0
    if (typeof val === 'object') return Object.keys(val).length > 0
    return true
}
</script>

<template>
    <div v-if="hasContent(hasilLab) || hasContent(hasilRadiologi)"
        class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
        <!-- Header -->
        <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
            <div
                class="w-7 h-7 rounded-xl bg-purple-500/10 text-purple-500 font-bold text-sm flex items-center justify-center">
                🧪
            </div>
            <div>
                <h3 class="text-xs font-black text-base-content">Hasil Laboratorium & Radiologi</h3>
                <p class="text-[9px] text-base-content/50">Hasil pemeriksaan penunjang medis</p>
            </div>
        </div>

        <div class="space-y-3 text-xs">
            <!-- Section Laboratorium -->
            <div v-if="hasContent(hasilLab)" class="space-y-1.5">
                <span
                    class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">Laboratorium</span>
                <div class="bg-base-200/40 p-3 rounded-2xl border border-base-200 space-y-2">
                    <template v-if="Array.isArray(hasilLab)">
                        <div v-for="(lab, idx) in hasilLab" :key="idx"
                            class="flex items-center justify-between border-b border-base-200/60 pb-1.5 last:border-0 last:pb-0">
                            <div>
                                <span class="font-bold text-base-content text-[11px] block">
                                    {{ lab.nmItem || lab.namaPemeriksaan || lab.item || 'Pemeriksaan Lab' }}
                                </span>
                                <span class="text-[9px] text-base-content/50" v-if="lab.nilaiRujukan || lab.rujukan">
                                    Rujukan: {{ lab.nilaiRujukan || lab.rujukan }}
                                </span>
                            </div>
                            <span class="font-black text-primary text-xs">
                                {{ lab.hasil || lab.nilai || '-' }} {{ lab.satuan || '' }}
                            </span>
                        </div>
                    </template>
                    <template v-else>
                        <p class="font-medium text-base-content text-[11px] whitespace-pre-line">
                            {{ typeof hasilLab === 'string' ? hasilLab : JSON.stringify(hasilLab, null, 2) }}
                        </p>
                    </template>
                </div>
            </div>

            <!-- Section Radiologi -->
            <div v-if="hasContent(hasilRadiologi)" class="space-y-1.5">
                <span class="text-[10px] text-base-content/50 uppercase font-bold tracking-wider block">Radiologi /
                    Ekspertise</span>
                <div class="bg-base-200/40 p-3 rounded-2xl border border-base-200 space-y-3">
                    <template v-if="Array.isArray(hasilRadiologi)">
                        <div v-for="(rad, idx) in hasilRadiologi" :key="idx"
                            class="space-y-2 border-b border-base-200/60 pb-3 last:border-0 last:pb-0">

                            <!-- Nama Item & Dokter -->
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <!-- PEMBACAAN UTAMA DARI API: rad.nmItem -->
                                    <span class="font-bold text-primary text-[11px] block">
                                        🩻 {{ rad.nmItem || rad.pemeriksaan || 'Hasil Radiologi' }}
                                    </span>
                                    <span v-if="rad.nmDokterInterpretasi || rad.dokter"
                                        class="text-[9px] text-base-content/50 block">
                                        Dokter Radiologi: {{ rad.nmDokterInterpretasi || rad.dokter }}
                                    </span>
                                    <span v-if="rad.nmRadiografer" class="text-[9px] text-base-content/40 block">
                                        Radiografer: {{ rad.nmRadiografer }}
                                    </span>
                                </div>
                                <span v-if="rad.kdTrs" class="text-[9px] badge badge-ghost badge-sm font-mono shrink-0">
                                    {{ rad.kdTrs }}
                                </span>
                            </div>

                            <!-- Kesan / Hasil Radiologi -->
                            <div class="bg-base-100/70 p-2.5 rounded-xl border border-base-200">
                                <span class="text-[9px] font-bold text-base-content/50 uppercase block mb-1">Kesan /
                                    Interpretasi:</span>
                                <!-- PEMBACAAN UTAMA DARI API: rad.kesan -->
                                <p
                                    class="text-[10px] font-medium text-base-content leading-relaxed whitespace-pre-line">
                                    {{ rad.kesan || rad.hasil || '-' }}
                                </p>
                            </div>

                            <!-- Thumbnail Gambar Hasil Radiologi (Base64 Array) -->
                            <div v-if="rad.file && rad.file.length > 0" class="space-y-1 pt-1">
                                <span class="text-[9px] font-bold text-base-content/50 block">Foto Radiologi ({{
                                    rad.file.length }} Gambar):</span>
                                <div class="flex gap-2 overflow-x-auto pb-1">
                                    <div v-for="(imgObj, imgIdx) in rad.file" :key="imgIdx"
                                        @click="openModal(imgObj.file)"
                                        class="relative group cursor-pointer border border-base-300 rounded-xl overflow-hidden shrink-0 w-16 h-16 bg-black/5">
                                        <img :src="imgObj.file" alt="Foto Radiologi"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                        <div
                                            class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition-opacity">
                                            Lihat
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>

                    <template v-else>
                        <p class="text-[10px] font-medium text-base-content/80 whitespace-pre-line">
                            {{ typeof hasilRadiologi === 'string' ? hasilRadiologi : JSON.stringify(hasilRadiologi,
                            null, 2) }}
                        </p>
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal Viewer Gambar Radiologi -->
        <div v-if="activeImage"
            class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4"
            @click.self="closeModal">
            <div
                class="relative max-w-3xl w-full bg-base-100 rounded-3xl overflow-hidden shadow-2xl border border-base-300">
                <div class="flex items-center justify-between p-3 border-b border-base-200 bg-base-100">
                    <span class="text-xs font-bold">Preview Foto Radiologi</span>
                    <button @click="closeModal" class="btn btn-xs btn-circle btn-ghost">✕</button>
                </div>
                <div class="p-2 bg-black flex items-center justify-center max-h-[80vh] overflow-auto">
                    <img :src="activeImage" alt="Radiologi Full"
                        class="max-w-full max-h-[75vh] object-contain rounded-lg" />
                </div>
            </div>
        </div>
    </div>
</template>
