<script setup>
import { ref } from 'vue'

defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close'])

const modalBoxRef = ref(null)
const startY = ref(0)
const currentY = ref(0)
const translateY = ref(0)
const isDragging = ref(false)

// Batas minimum tarikan (dalam px) untuk menutup modal
const DISMISS_THRESHOLD = 80 

const onTouchStart = (e) => {
  // Hanya izinkan swipe jika scroll modal sedang di paling atas
  if (modalBoxRef.value && modalBoxRef.value.scrollTop > 0) return

  startY.value = e.touches[0].clientY
  isDragging.value = true
}

const onTouchMove = (e) => {
  if (!isDragging.value) return

  currentY.value = e.touches[0].clientY
  const delta = currentY.value - startY.value

  // Hanya izinkan tarikan ke arah bawah (nilai positif)
  if (delta > 0) {
    translateY.value = delta
  }
}

const onTouchEnd = () => {
  if (!isDragging.value) return

  if (translateY.value > DISMISS_THRESHOLD) {
    // Geser melebihi batas -> tutup modal
    translateY.value = 300
    setTimeout(() => {
      emit('close')
      translateY.value = 0
    }, 150)
  } else {
    // Balikkan ke posisi semula (snap back)
    translateY.value = 0
  }

  isDragging.value = false
}
</script>

<template>
  <Teleport to="body">
    <dialog class="modal modal-bottom sm:modal-middle" :class="{ 'modal-open': show }">
      <div 
        v-if="show"
        ref="modalBoxRef"
        @touchstart="onTouchStart"
        @touchmove="onTouchMove"
        @touchend="onTouchEnd"
        class="modal-box p-5 pt-3 space-y-3.5 rounded-t-3xl sm:rounded-3xl border border-base-300 bg-base-100 max-h-[85vh] overflow-y-auto no-scrollbar shadow-2xl transition-transform"
        :class="{ 'duration-200 ease-out': !isDragging }"
        :style="{ transform: `translateY(${translateY}px)` }"
      >
        <!-- 1. Interactive Drag Handle Pill (Gesture Trigger) -->
        <div class="flex justify-center pb-1 pt-0.5 cursor-grab active:cursor-grabbing touch-none">
          <div class="w-12 h-1.5 rounded-full bg-base-300 hover:bg-base-content/20 transition-colors"></div>
        </div>

        <!-- 2. Modal Header -->
        <div class="flex items-start justify-between pb-2 border-b border-base-200 gap-3">
          <div class="flex items-center gap-2.5 min-w-0">
            <!-- Icon Box -->
            <div 
              v-if="icon || $slots.icon" 
              class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-base shrink-0 shadow-2xs"
            >
              <slot name="icon">{{ icon }}</slot>
            </div>
            
            <div class="min-w-0">
              <slot name="header">
                <h3 class="text-sm font-bold text-base-content leading-snug truncate">
                  {{ title }}
                </h3>
                <p v-if="subtitle" class="text-[11px] text-base-content/60 leading-tight truncate">
                  {{ subtitle }}
                </p>
              </slot>
            </div>
          </div>

          <!-- Close Button -->
          <button 
            @click="$emit('close')" 
            class="btn btn-sm btn-circle btn-ghost text-base-content/60 hover:text-base-content shrink-0"
            aria-label="Tutup Modal"
          >
            ✕
          </button>
        </div>

        <!-- 3. Modal Body Content -->
        <div class="space-y-3">
          <slot></slot>
        </div>

        <!-- 4. Modal Action Footer -->
        <div class="modal-action mt-2 pt-2 border-t border-base-200">
          <slot name="action">
            <button @click="$emit('close')" class="btn btn-sm btn-block btn-neutral rounded-xl font-medium">
              Tutup
            </button>
          </slot>
        </div>
      </div>

      <!-- Backdrop Area (Click outside to close) -->
      <form method="dialog" class="modal-backdrop bg-neutral/40 backdrop-blur-xs">
        <button type="button" @click="$emit('close')">close</button>
      </form>
    </dialog>
  </Teleport>
</template>