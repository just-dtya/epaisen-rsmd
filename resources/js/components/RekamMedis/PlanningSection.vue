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
    <div class="flex items-center gap-2 border-b border-base-200 pb-2.5">
      <div class="w-7 h-7 rounded-xl bg-indigo-500/10 text-indigo-500 font-bold text-sm flex items-center justify-center">
        📝
      </div>
      <div>
        <h3 class="text-xs font-black text-base-content">Implementasi Keperawatan</h3>
        <p class="text-[9px] text-base-content/50">Tindakan & edukasi pelayanan kesehatan</p>
      </div>
    </div>

    <div class="p-3 bg-base-200/40 rounded-2xl border border-base-200 text-xs font-medium leading-relaxed text-base-content/80 whitespace-pre-line">
      {{ cleanText(implementasi) }}
    </div>
  </div>
</template>
