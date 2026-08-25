<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  no_identitas: '',
  tgl_lahir: ''
})

const errorMessage = ref('')
let toastTimer = null

const triggerToast = (msg) => {
  errorMessage.value = msg
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => {
    errorMessage.value = ''
  }, 4000)
}

const handleLogin = () => {
  if (!form.no_identitas.trim()) {
    triggerToast('Silakan isi No. Rekam Medis atau NIK KTP Anda.')
    return
  }
  if (!form.tgl_lahir) {
    triggerToast('Silakan pilih tanggal lahir Anda.')
    return
  }

  form.post('/login', {
    preserveScroll: true,
    onError: (errors) => {
      if (errors.login_error) {
        triggerToast(errors.login_error)
      } else if (errors.no_identitas) {
        triggerToast(errors.no_identitas)
      } else if (errors.tgl_lahir) {
        triggerToast(errors.tgl_lahir)
      } else {
        const firstErr = Object.values(errors)[0]
        if (firstErr) triggerToast(firstErr)
      }
    }
  })
}
</script>

<template>
  <Head title="Masuk - ePasien RSMD" />

  <main class="min-h-screen bg-base-200 flex flex-col justify-center items-center p-4 relative overflow-hidden select-none">

    <!-- Ambient Background Glow -->
    <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-primary/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -right-20 w-72 h-72 rounded-full bg-secondary/10 blur-3xl pointer-events-none"></div>

    <!-- Floating DaisyUI Error Toast -->
    <Transition name="toast-slide">
      <div v-if="errorMessage" class="toast toast-top toast-center z-50 p-4 max-w-sm w-full">
        <div class="alert alert-error text-xs shadow-lg rounded-2xl flex items-center justify-between py-3 px-4 border border-error/20 text-white">
          <div class="flex items-center gap-2.5 min-w-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold leading-tight break-words">{{ errorMessage }}</span>
          </div>
          <button type="button" @click="errorMessage = ''" class="btn btn-ghost btn-xs btn-circle shrink-0 text-white">✕</button>
        </div>
      </div>
    </Transition>

    <div class="w-full max-w-sm relative z-10 space-y-5">

      <!-- Brand Header -->
      <div class="text-center space-y-2">
        <div class="relative inline-block">
          <div class="w-18 h-18 rounded-3xl bg-base-100 p-3.5 border border-base-300 shadow-sm ring-4 ring-primary/10 flex items-center justify-center mx-auto transition-transform hover:scale-105">
            <img
              src="/icon_rsmd.png"
              alt="Logo RSMD"
              class="w-full h-full object-contain"
            />
          </div>
          <span class="absolute bottom-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-base-100 border border-base-300 shadow-xs">
            <span class="h-2.5 w-2.5 rounded-full bg-success animate-pulse"></span>
          </span>
        </div>

        <div>
          <h1 class="text-2xl font-black text-base-content tracking-tight">
            RSMD<span class="text-primary font-bold">Mobile</span>
          </h1>
          <p class="text-xs text-base-content/60 font-medium">Portal Layanan & Rekam Medis Pasien</p>
        </div>
      </div>

      <!-- Main Login Card -->
      <div class="card bg-base-100/90 backdrop-blur-md rounded-3xl border border-base-300 shadow-md p-6 space-y-4">

        <div class="border-b border-base-200 pb-3">
          <h2 class="text-sm font-bold text-base-content">Masuk Pasien</h2>
          <p class="text-[11px] text-base-content/60">Verifikasi dengan No. Rekam Medis & Tanggal Lahir.</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-3.5">

          <!-- No. Rekam Medis / NIK Input -->
          <div class="space-y-1.5">
            <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 flex items-center justify-between">
              <span>No. RM / NIK KTP</span>
              <span class="text-[10px] text-base-content/40 font-normal">Wajib</span>
            </label>
            <div class="relative">
              <input
                v-model="form.no_identitas"
                type="text"
                placeholder="Contoh: 012345 atau 3302..."
                class="input input-sm input-bordered w-full bg-base-200/50 pl-9 rounded-xl text-xs font-medium focus:input-primary transition-all h-10 font-mono"
                :class="{ 'input-error': form.errors.no_identitas }"
                :disabled="form.processing"
                autocomplete="username"
                required
              />
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
              </svg>
            </div>
            <p v-if="form.errors.no_identitas" class="text-error text-[10px]">{{ form.errors.no_identitas }}</p>
          </div>

          <!-- Tanggal Lahir Input -->
          <div class="space-y-1.5">
            <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 flex items-center justify-between">
              <span>Tanggal Lahir</span>
              <span class="text-[10px] text-base-content/40 font-normal">YYYY-MM-DD</span>
            </label>
            <div class="relative">
              <input
                v-model="form.tgl_lahir"
                type="date"
                class="input input-sm input-bordered w-full bg-base-200/50 pl-9 rounded-xl text-xs font-medium focus:input-primary transition-all h-10 font-mono"
                :class="{ 'input-error': form.errors.tgl_lahir }"
                :disabled="form.processing"
                required
              />
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <p v-if="form.errors.tgl_lahir" class="text-error text-[10px]">{{ form.errors.tgl_lahir }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="btn btn-primary btn-block rounded-xl mt-3 font-bold text-xs shadow-md shadow-primary/20 h-11 text-white"
            :disabled="form.processing"
          >
            <span v-if="form.processing" class="loading loading-spinner loading-xs"></span>
            <span>{{ form.processing ? 'Memverifikasi Pasien...' : 'Masuk Portal Pasien' }}</span>
          </button>

          <!-- Registrasi Pasien Baru Link -->
          <div class="text-center pt-2 border-t border-base-200">
            <p class="text-xs text-base-content/60">
              Belum pernah berobat?
              <Link href="/daftar" class="text-primary font-bold hover:underline ml-1">
                Daftar Pasien Baru
              </Link>
            </p>
          </div>

        </form>

      </div>

      <!-- Footer Info -->
      <div class="text-center space-y-1">
        <p class="text-[11px] text-base-content/50 font-medium">
          RSMD Soepardjo Roestam
        </p>
      </div>

    </div>

  </main>
</template>

<style scoped>
.toast-slide-enter-active,
.toast-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-slide-enter-from,
.toast-slide-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}
</style>
