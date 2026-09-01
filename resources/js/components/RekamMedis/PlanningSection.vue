<script setup>
const props = defineProps({
  implementasi: {
    type: String,
    default: ''
  }
})

const cleanText = (str) => {
  if (!str) return ''
  return str.replace(/\\t/g, ' ').replace(/\t/g, ' ').trim()
}

const hasContent = (val) => {
  return val && val.trim() !== ''
}
</script>

<template>
  <div v-if="hasContent(implementasi)" class="card bg-base-100 border border-base-300 shadow-2xs rounded-3xl p-4 space-y-3">
    <!-- Header -->
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center shrink-0">
        <!-- Ikon Document / Clipboard Checklist -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>
      </div>
      <div>
        <h3 class="text-xs font-black text-base-content">Implementasi Keperawatan</h3>
        <p class="text-[9px] text-base-content/50">Tindakan & edukasi pelayanan kesehatan</p>
      </div>
    </div>

    <!-- Content Block -->
    <div class="p-3 bg-base-200/40 rounded-2xl border border-base-200 text-xs font-medium leading-relaxed text-base-content/80 whitespace-pre-line">
      {{ cleanText(implementasi) }}
    </div>
  </div>
</template>
