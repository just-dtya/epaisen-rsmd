<script setup>
import { watch, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close'])

const closeModal = () => {
  emit('close')
}

// Menangani tombol ESC keyboard
const handleKeyDown = (e) => {
  if (e.key === 'Escape' && props.show) {
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

// Prevent body scroll saat modal terbuka
watch(() => props.show, (newVal) => {
  if (typeof document !== 'undefined') {
    if (newVal) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})
</script>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
      >
        <!-- Backdrop Blur -->
        <div
          @click="closeModal"
          class="fixed inset-0 bg-neutral-900/60 backdrop-blur-md transition-opacity"
        ></div>

        <!-- Dialog Container -->
        <div
          class="relative w-full max-w-md bg-base-100 rounded-3xl shadow-2xl border border-base-200/80 overflow-hidden z-10 flex flex-col max-h-[85vh] animate-modal-pop"
        >
          <!-- Header Modal -->
          <div class="px-5 py-4 border-b border-base-200 flex items-center justify-between shrink-0 bg-base-100">
            <div>
              <h3 class="font-black text-base text-base-content tracking-tight">Semua Layanan ePasien</h3>
              <p class="text-[11px] text-base-content/50 font-medium">Direktori modul & portal kesehatan RSMD</p>
            </div>
            <button
              @click="closeModal"
              class="btn btn-sm btn-circle btn-ghost text-base-content/60 hover:bg-base-200"
              aria-label="Tutup Modal"
            >
              ✕
            </button>
          </div>

          <!-- Body Content (Scrollable) -->
          <div class="p-5 overflow-y-auto space-y-5 custom-scrollbar">
            <div v-for="(cat, cIdx) in categories" :key="cIdx" class="space-y-2.5">
              <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40 px-1 block">
                {{ cat.title }}
              </span>

              <div class="space-y-2">
                <Link
                  v-for="item in cat.items"
                  :key="item.id"
                  :href="item.href"
                  @click="closeModal"
                  class="flex items-center gap-3 p-3 rounded-2xl bg-base-200/50 hover:bg-base-200 border border-base-300/40 active:scale-[0.98] transition-all group"
                >
                  <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-2xs"
                    :class="item.color"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                      <h4 class="text-xs font-bold text-base-content group-hover:text-primary transition-colors">
                        {{ item.label }}
                      </h4>
                      <span
                        v-if="item.badge"
                        class="badge badge-error text-white text-[8px] font-bold px-1.5 py-0 h-3.5 rounded-md animate-pulse"
                      >
                        {{ item.badge }}
                      </span>
                    </div>
                    <p class="text-[10px] text-base-content/50 truncate mt-0.5">
                      {{ item.desc }}
                    </p>
                  </div>

                  <span class="text-base-content/30 group-hover:text-primary transition-colors text-xs font-bold pr-1">
                    →
                  </span>
                </Link>
              </div>
            </div>
          </div>

          <!-- Footer Action -->
          <div class="p-4 bg-base-200/50 border-t border-base-200 shrink-0">
            <button
              @click="closeModal"
              class="btn btn-primary btn-sm rounded-2xl text-white font-bold w-full shadow-md shadow-primary/20 hover:shadow-primary/40 transition-all"
            >
              Tutup
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
