<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  posts: {
    type: Array,
    default: () => []
  }
})

const searchQuery = ref('')
const selectedArticle = ref(null)
const isRefreshing = ref(false)
const copied = ref(false)

// Refresh dengan cache-busting ke BeritaController
const handleRefresh = () => {
  isRefreshing.value = true
  router.get(
    '/berita',
    { refresh: 1 },
    {
      preserveScroll: true,
      preserveState: true,
      onFinish: () => {
        isRefreshing.value = false
      }
    }
  )
}

// Estimasi waktu baca (rata-rata 180 kata/menit)
const calculateReadTime = (text) => {
  if (!text) return '1 mnt baca'
  const words = text.replace(/<[^>]*>?/gm, '').trim().split(/\s+/).length
  const minutes = Math.max(1, Math.ceil(words / 180))
  return `${minutes} mnt baca`
}

// Filter artikel berdasarkan kata kunci
const filteredPosts = computed(() => {
  if (!props.posts || !Array.isArray(props.posts)) return []
  if (!searchQuery.value.trim()) return props.posts

  const q = searchQuery.value.toLowerCase().trim()
  return props.posts.filter((p) => {
    const titleMatch = p.title?.toLowerCase().includes(q)
    const excerptMatch = p.excerpt?.toLowerCase().includes(q)
    const authorMatch = p.author?.toLowerCase().includes(q)
    return titleMatch || excerptMatch || authorMatch
  })
})

// Berita Utama (Headline) vs Berita Lainnya
const headlinePost = computed(() => {
  return filteredPosts.value.length > 0 ? filteredPosts.value[0] : null
})

const regularPosts = computed(() => {
  return filteredPosts.value.length > 1 ? filteredPosts.value.slice(1) : []
})

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
  <Head title="Berita & Edukasi Kesehatan - RSMD" />

  <AppLayout>
    <main class="p-4 space-y-4 max-w-md mx-auto w-full select-none pb-24">

      <!-- 1. Header Banner Portal -->
      <div class="flex items-center justify-between px-1 pt-1">
        <div>
          <span class="text-[9px] font-bold uppercase tracking-wider text-primary bg-primary/10 px-2 py-0.5 rounded-md">
            Warta & Informasi
          </span>
          <h1 class="text-lg font-black tracking-tight text-base-content mt-1 leading-tight">
            Kabar RSMD
          </h1>
          <p class="text-[11px] text-base-content/50">
            Publikasi resmi, inovasi pelayanan, & edukasi kesehatan
          </p>
        </div>

        <button
          @click="handleRefresh"
          :disabled="isRefreshing"
          class="btn btn-sm btn-circle btn-ghost bg-base-100 border border-base-300 shadow-2xs text-base-content/60 hover:text-primary transition-all active:scale-95 shrink-0"
          title="Segarkan Berita"
          aria-label="Segarkan Berita"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            :class="{ 'animate-spin text-primary': isRefreshing }"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>

      <!-- 2. Search Box Filter -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari artikel, topik, atau kegiatan..."
          class="input input-sm w-full bg-base-100 focus:bg-base-100 rounded-2xl pl-9 pr-8 text-xs focus:outline-primary border-base-300 shadow-2xs transition-all"
        />
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-2.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <button
          v-if="searchQuery"
          @click="searchQuery = ''"
          class="btn btn-xs btn-circle btn-ghost absolute right-1.5 top-1.5 text-base-content/40"
          aria-label="Hapus Pencarian"
        >
          ✕
        </button>
      </div>

      <!-- 3. Empty State -->
      <div v-if="filteredPosts.length === 0" class="card bg-base-100 border border-base-300 p-8 text-center rounded-3xl space-y-2 shadow-2xs">
        <div class="w-12 h-12 bg-base-200 rounded-2xl flex items-center justify-center mx-auto text-xl">
          📰
        </div>
        <h2 class="text-xs font-bold text-base-content">Artikel tidak ditemukan</h2>
        <p class="text-[11px] text-base-content/50">Coba gunakan kata kunci pencarian yang lain.</p>
        <button @click="searchQuery = ''" class="btn btn-xs btn-primary rounded-xl text-white font-bold mx-auto mt-1">
          Tampilkan Semua
        </button>
      </div>

      <template v-else>
        <!-- 4. Headline / Featured Post Card -->
        <article
          v-if="headlinePost && !searchQuery"
          @click="selectedArticle = headlinePost"
          class="group relative overflow-hidden rounded-3xl bg-neutral text-white shadow-md cursor-pointer transition-all duration-300 active:scale-[0.99]"
        >
          <!-- Thumbnail Background with Gradient -->
          <div class="aspect-[16/10] w-full bg-base-300 overflow-hidden relative">
            <img
              v-if="headlinePost.thumbnail"
              :src="headlinePost.thumbnail"
              :alt="headlinePost.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-3xl bg-base-300 text-base-content/20">
              📰
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
          </div>

          <!-- Headline Meta & Title Over Overlay -->
          <div class="absolute bottom-0 inset-x-0 p-4 space-y-2">
            <div class="flex items-center gap-2">
              <span class="badge badge-primary text-[9px] font-extrabold px-2 py-0.5 border-0 uppercase tracking-wider">
                Terbaru
              </span>
              <span class="text-[10px] text-white/75 font-medium flex items-center gap-1">
                📅 {{ headlinePost.date }}
              </span>
              <span class="text-[10px] text-white/50">•</span>
              <span class="text-[10px] text-emerald-400 font-semibold">
                {{ calculateReadTime(headlinePost.content) }}
              </span>
            </div>

            <h2 class="text-sm font-black tracking-tight leading-snug text-white line-clamp-2 drop-shadow-xs">
              {{ headlinePost.title }}
            </h2>

            <p class="text-[11px] text-white/75 line-clamp-2 font-normal leading-relaxed">
              {{ headlinePost.excerpt }}
            </p>
          </div>
        </article>

        <!-- 5. List Feed Berita Lainnya -->
        <div class="space-y-3 pt-1">
          <div class="flex items-center justify-between px-1 text-[11px] font-bold text-base-content/60">
            <span>{{ searchQuery ? 'Hasil Pencarian' : 'Kabar Lainnya' }}</span>
            <span>{{ filteredPosts.length }} Artikel</span>
          </div>

          <!-- Card Item Berita -->
          <article
            v-for="post in (searchQuery ? filteredPosts : regularPosts)"
            :key="post.id"
            @click="selectedArticle = post"
            class="card bg-base-100 border border-base-300 shadow-2xs hover:border-primary/40 rounded-3xl p-3.5 flex flex-row gap-3.5 cursor-pointer transition-all duration-150 active:scale-[0.99] group"
          >
            <!-- Thumbnail Kecil -->
            <div class="w-24 h-24 rounded-2xl bg-base-200 overflow-hidden shrink-0 relative">
              <img
                v-if="post.thumbnail"
                :src="post.thumbnail"
                :alt="post.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-xl text-base-content/20 bg-base-300">
                📰
              </div>
            </div>

            <!-- Detail Teks -->
            <div class="flex-1 flex flex-col justify-between py-0.5 min-w-0">
              <div class="space-y-1">
                <div class="flex items-center gap-1.5 text-[9px] font-bold text-base-content/50">
                  <span>{{ post.date }}</span>
                  <span>•</span>
                  <span class="text-primary">{{ calculateReadTime(post.content) }}</span>
                </div>

                <h3 class="text-xs font-extrabold text-base-content tracking-tight leading-snug group-hover:text-primary transition-colors line-clamp-2">
                  {{ post.title }}
                </h3>

                <p class="text-[10px] text-base-content/60 line-clamp-2 leading-relaxed">
                  {{ post.excerpt }}
                </p>
              </div>

              <div class="flex items-center justify-between pt-1 text-[10px] text-base-content/40 font-medium">
                <span class="truncate max-w-[100px]">✍️ {{ post.author }}</span>
                <span class="text-primary font-bold text-[10px] group-hover:translate-x-0.5 transition-transform">
                  Baca →
                </span>
              </div>
            </div>
          </article>
        </div>
      </template>

      <!-- 6. Modal Reader Artikel (Full Typography View) -->
      <dialog class="modal" :class="{ 'modal-open': selectedArticle !== null }">
        <div v-if="selectedArticle" class="modal-box rounded-3xl border border-base-300 max-w-lg p-5 max-h-[92vh] overflow-y-auto space-y-4">

          <!-- Modal Top Header -->
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

          <!-- Judul Artikel Modal -->
          <h2 class="font-black text-base text-base-content tracking-tight leading-snug">
            {{ selectedArticle.title }}
          </h2>

          <div class="flex items-center gap-2 text-[10px] text-base-content/50 font-medium pb-1">
            <span>Ditulis oleh: <strong class="text-base-content/80">{{ selectedArticle.author }}</strong></span>
            <span>•</span>
            <span>Sumber: PPID RSMD</span>
          </div>

          <!-- Featured Image di Reader -->
          <div v-if="selectedArticle.thumbnail" class="rounded-2xl overflow-hidden aspect-video bg-base-200">
            <img :src="selectedArticle.thumbnail" :alt="selectedArticle.title" class="w-full h-full object-cover" />
          </div>

          <!-- Isi Konten Artikel HTML Lengkap dari WordPress -->
          <div
            class="text-xs leading-relaxed text-base-content/85 space-y-3 prose-sm max-w-none [&_p]:mb-3 [&_p]:leading-relaxed [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1.5 [&_figure]:my-3.5 [&_img]:rounded-2xl [&_img]:w-full [&_img]:shadow-2xs"
            v-html="selectedArticle.content"
          ></div>

          <!-- Action Footer Modal -->
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

    </main>
  </AppLayout>
</template>
