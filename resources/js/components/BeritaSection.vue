<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  berita: {
    type: Array,
    default: () => []
  }
})

const selectedArticle = ref(null)
const copied = ref(false)

// Estimasi waktu baca
const calculateReadTime = (text) => {
  if (!text) return '1 mnt baca'
  const words = text.replace(/<[^>]*>?/gm, '').trim().split(/\s+/).length
  const minutes = Math.max(1, Math.ceil(words / 180))
  return `${minutes} mnt baca`
}

// Share artikel via Web Share API atau copy link
const shareArticle = async (article) => {
  if (!article) return
  const shareData = {
    title: article.title,
    text: article.excerpt,
    url: article.link && article.link !== '#' ? article.link : window.location.href
  }

  if (navigator.share) {
    try {
      await navigator.share(shareData)
    } catch {
      // User membatalkan dialog share
    }
  } else {
    try {
      await navigator.clipboard.writeText(shareData.url)
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    } catch {
      // Fallback silent
    }
  }
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

    <!-- Modal Reader Artikel -->
    <dialog class="modal" :class="{ 'modal-open': selectedArticle !== null }">
      <div v-if="selectedArticle" class="modal-box rounded-3xl border border-base-300 max-w-md p-5 max-h-[90vh] overflow-y-auto space-y-4">

        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b border-base-200 pb-3">
          <div class="flex items-center gap-2">
            <span class="badge badge-primary/10 text-primary border-0 text-[10px] font-bold px-2 py-0.5 rounded-md">
              {{ selectedArticle.date }}
            </span>
            <span class="text-[10px] text-base-content/50 font-medium">
              {{ calculateReadTime(selectedArticle.content) }}
            </span>
          </div>

          <div class="flex items-center gap-1">
            <button
              @click="shareArticle(selectedArticle)"
              class="btn btn-xs btn-circle btn-ghost text-base-content/60 hover:text-primary"
              title="Bagikan Artikel"
              aria-label="Bagikan Artikel"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
              </svg>
            </button>
            <button
              @click="selectedArticle = null"
              class="btn btn-sm btn-circle btn-ghost text-base-content/60"
              aria-label="Tutup Modal"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Judul & Penulis -->
        <div>
          <h2 class="font-black text-base text-base-content tracking-tight leading-snug">
            {{ selectedArticle.title }}
          </h2>
          <div class="flex items-center gap-2 text-[10px] text-base-content/50 font-medium pt-1">
            <span>Ditulis oleh: <strong class="text-base-content/80">{{ selectedArticle.author || 'PPID RSMD' }}</strong></span>
            <span>•</span>
            <span>Sumber: PPID RSMD</span>
          </div>
        </div>

        <!-- Featured Image -->
        <div v-if="selectedArticle.thumbnail" class="rounded-2xl overflow-hidden aspect-video bg-base-200">
          <img :src="selectedArticle.thumbnail" :alt="selectedArticle.title" class="w-full h-full object-cover" />
        </div>

        <!-- Render Konten HTML -->
        <div
          class="text-xs leading-relaxed text-base-content/85 space-y-3 prose-sm max-w-none [&_p]:mb-3 [&_p]:leading-relaxed [&_ul]:list-disc [&_ul]:pl-5 [&_figure]:my-3.5 [&_img]:rounded-2xl [&_img]:w-full"
          v-html="selectedArticle.content"
        ></div>

        <!-- Modal Action -->
        <div class="modal-action pt-2 flex items-center gap-2">
          <button
            @click="shareArticle(selectedArticle)"
            class="btn btn-sm btn-ghost border border-base-300 rounded-xl text-xs font-bold gap-1 flex-1"
          >
            <span>🔗</span>
            <span>{{ copied ? 'Tautan Disalin!' : 'Bagikan' }}</span>
          </button>
          <button
            @click="selectedArticle = null"
            class="btn btn-sm btn-primary rounded-xl text-white font-bold flex-1"
          >
            Tutup Berita
          </button>
        </div>

      </div>
    </dialog>
  </section>
</template>
