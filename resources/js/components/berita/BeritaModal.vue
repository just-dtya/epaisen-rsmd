<script setup>
import { watch, onUnmounted } from 'vue'

const props = defineProps({
  article: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close'])

// Menangani tombol ESC keyboard
const handleKeyDown = (e) => {
  if (e.key === 'Escape' && props.article) {
    closeModal()
  }
}

if (typeof window !== 'undefined') {
  window.addEventListener('keydown', handleKeyDown)
}

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeyDown)
  }
})

// Mencegah scroll body saat modal terbuka
watch(() => props.article, (newVal) => {
  if (typeof document !== 'undefined') {
    if (newVal) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})

const closeModal = () => {
  emit('close')
}

// Estimasi waktu baca
const calculateReadTime = (text) => {
  if (!text) return '1 mnt baca'
  const words = text.replace(/<[^>]*>?/gm, '').trim().split(/\s+/).length
  const minutes = Math.max(1, Math.ceil(words / 180))
  return `${minutes} mnt baca`
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="article"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
      >
        <!-- Backdrop Blur -->
        <div
          @click="closeModal"
          class="fixed inset-0 bg-neutral-900/60 backdrop-blur-md transition-opacity"
        ></div>

        <!-- Dialog Container -->
        <div
          class="relative w-full max-w-lg bg-base-100 rounded-3xl shadow-2xl border border-base-200/80 overflow-hidden z-10 flex flex-col max-h-[90vh] animate-modal-pop"
        >
          <!-- Hero Header Image / Banner -->
          <div class="relative h-48 sm:h-56 w-full bg-base-300 shrink-0 overflow-hidden">
            <img
              v-if="article.thumbnail"
              :src="article.thumbnail"
              :alt="article.title"
              class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
            />
            <div
              v-else
              class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-primary/10 via-base-200 to-base-300 text-base-content/30"
            >
              <span class="text-4xl mb-1">📰</span>
              <span class="text-xs font-semibold uppercase tracking-widest">Kabar RSMD</span>
            </div>

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-base-100 via-transparent to-black/40"></div>

            <!-- Floating Top Bar -->
            <div class="absolute top-3 left-3 right-3 flex items-center justify-between z-20">
              <span class="px-2.5 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[10px] font-medium border border-white/20 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>{{ article.date }}</span>
              </span>

              <button
                @click="closeModal"
                class="btn btn-circle btn-sm bg-black/40 hover:bg-black/60 backdrop-blur-md text-white border-white/20 hover:border-white/40 border transition-all active:scale-95"
                title="Tutup"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Reading Time Badge -->
            <div class="absolute bottom-3 left-4 z-20">
              <span class="badge badge-primary font-bold text-[10px] px-2.5 py-1 shadow-lg border-0">
                ⏱️ {{ calculateReadTime(article.content) }}
              </span>
            </div>
          </div>

          <!-- Body Content (Scrollable) -->
          <div class="p-5 sm:p-6 overflow-y-auto space-y-4 custom-scrollbar">
            <!-- Author & Metadata -->
            <div class="flex items-center gap-2 text-[11px] text-base-content/60 font-medium">
              <div class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[10px]">
                {{ (article.author || 'P').charAt(0) }}
              </div>
              <span>Oleh <strong class="text-base-content">{{ article.author || 'PPID RSMD' }}</strong></span>
              <span>•</span>
              <span class="text-primary font-semibold">PPID RSMD</span>
            </div>

            <!-- Judul Artikel -->
            <h2 class="text-lg sm:text-xl font-black text-base-content tracking-tight leading-snug">
              {{ article.title }}
            </h2>

            <hr class="border-base-200" />

            <!-- Render HTML Konten -->
            <div
              class="text-xs sm:text-sm leading-relaxed text-base-content/85 space-y-3 prose-sm max-w-none [&_p]:leading-relaxed [&_p]:mb-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_img]:rounded-2xl [&_img]:shadow-md [&_img]:my-3 [&_blockquote]:border-l-4 [&_blockquote]:border-primary [&_blockquote]:pl-3 [&_blockquote]:italic"
              v-html="article.content"
            ></div>
          </div>

          <!-- Footer Action -->
          <div class="p-4 bg-base-200/50 border-t border-base-200 flex items-center shrink-0">
            <button
              @click="closeModal"
              class="btn btn-primary btn-sm rounded-2xl text-white font-bold w-full shadow-md shadow-primary/20 hover:shadow-primary/40 transition-all"
            >
              Tutup Berita
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/* Transisi Modal Fade */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* Transisi Zoom Pop Modal */
@keyframes modalPop {
  0% {
    opacity: 0;
    transform: scale(0.92) translateY(10px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
.animate-modal-pop {
  animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(150, 150, 150, 0.2);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(150, 150, 150, 0.4);
}
</style>
