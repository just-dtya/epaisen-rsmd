<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import BeritaModal from '../components/berita/BeritaModal.vue'

const props = defineProps({
  berita: {
    type: Array,
    default: () => []
  }
})

const selectedArticle = ref(null)

// Estimasi waktu baca
const calculateReadTime = (text) => {
  if (!text) return '1 mnt baca'
  const words = text.replace(/<[^>]*>?/gm, '').trim().split(/\s+/).length
  const minutes = Math.max(1, Math.ceil(words / 180))
  return `${minutes} mnt baca`
}
</script>

<template>
  <section class="space-y-2.5 pt-1">
    <!-- Header Section -->
    <div class="flex items-center justify-between px-1">
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-primary"></span>
        <h3 class="text-xs font-bold uppercase tracking-wider text-base-content/80">
          Kabar RSMD
        </h3>
      </div>
      <Link
        href="/berita"
        class="text-[11px] text-primary font-bold hover:underline inline-flex items-center gap-0.5"
      >
        Lihat Semua
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </Link>
    </div>

    <!-- Empty State -->
    <div v-if="!berita || berita.length === 0" class="card bg-base-100 border border-base-300 p-6 text-center text-xs text-base-content/60 rounded-2xl">
      Belum ada warta terbaru yang dipublikasikan.
    </div>

    <!-- Horizontal Scrollable Cards -->
    <div v-else class="flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 no-scrollbar">
      <article
        v-for="item in berita"
        :key="item.id"
        @click="selectedArticle = item"
        class="card bg-base-100 border border-base-300 shadow-2xs rounded-2xl w-60 shrink-0 cursor-pointer hover:border-primary/40 hover:shadow-xs transition-all overflow-hidden flex flex-col justify-between group active:scale-[0.99]"
      >
        <!-- Thumbnail -->
        <div class="h-28 w-full bg-base-200 overflow-hidden relative shrink-0">
          <img
            v-if="item.thumbnail"
            :src="item.thumbnail"
            :alt="item.title"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
          />
          <div v-else class="w-full h-full flex items-center justify-center text-2xl bg-base-300 text-base-content/20">
            📰
          </div>
        </div>

        <!-- Card Body -->
        <div class="p-3.5 space-y-1.5 flex-1 flex flex-col justify-between">
          <div class="space-y-1">
            <div class="flex items-center gap-1.5 text-[9px] font-bold text-base-content/50">
              <span>{{ item.date }}</span>
              <span>•</span>
              <span class="text-primary">{{ calculateReadTime(item.content) }}</span>
            </div>

            <h4 class="text-xs font-bold text-base-content line-clamp-2 leading-snug group-hover:text-primary transition-colors">
              {{ item.title }}
            </h4>

            <p class="text-[10px] text-base-content/60 line-clamp-2 leading-relaxed">
              {{ item.excerpt }}
            </p>
          </div>

          <div class="pt-2 border-t border-base-200/60 flex items-center justify-between text-[10px] text-primary font-bold">
            <span>Baca Selengkapnya</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </div>
        </div>
      </article>
    </div>

    <!-- Modal Component -->
    <BeritaModal
      :article="selectedArticle"
      @close="selectedArticle = null"
    />
  </section>
</template>
