<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  no_ktp: '',
  name: '',
  jk: 'L',
  tmp_lahir: '',
  tgl_lahir: '',
  nm_ibu: '',
  no_tlp: '',
  alamat: ''
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

const handleRegister = () => {
  if (form.no_ktp.length !== 16) {
    triggerToast('NIK KTP harus terdiri dari 16 digit angka.')
    return
  }

  form.post('/daftar', {
    preserveScroll: true,
    onError: (errors) => {
      if (errors.register_error) {
        triggerToast(errors.register_error)
      } else {
        const firstErr = Object.values(errors)[0]
        if (firstErr) triggerToast(firstErr)
      }
    }
  })
}
</script>

<template>
  <Head title="Daftar Pasien Baru - ePasien RSMD" />

  <main class="min-h-screen bg-base-200 flex flex-col justify-center items-center p-4 py-8 relative overflow-hidden select-none">

    <!-- Ambient Background Glow -->
    <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-primary/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-secondary/10 blur-3xl pointer-events-none"></div>

    <!-- Floating Toast Error -->
    <Transition name="toast-slide">
      <div v-if="errorMessage" class="toast toast-top toast-center z-50 p-4 max-w-md w-full">
        <div class="alert alert-error text-xs shadow-lg rounded-2xl flex items-center justify-between py-3 px-4 border border-error/20 text-white">
          <div class="flex items-center gap-2.5 min-w-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold leading-tight break-words">{{ errorMessage }}</span>
          </div>
          <button @click="errorMessage = ''" class="btn btn-ghost btn-xs btn-circle shrink-0 text-white">✕</button>
        </div>
      </div>
    </Transition>

    <div class="w-full max-w-md relative z-10 space-y-4">

      <!-- Card Form -->
      <div class="card bg-base-100/90 backdrop-blur-md rounded-3xl border border-base-300 shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-b from-primary/10 via-primary/5 to-base-100 p-5 text-center border-b border-base-200">
          <div class="w-12 h-12 rounded-2xl bg-base-100 p-2 mx-auto mb-2 shadow-2xs border border-base-300 flex items-center justify-center">
            <img src="/icon_rsmd.png" alt="Logo RSMD" class="w-full h-full object-contain" />
          </div>
          <h1 class="text-base font-black text-base-content tracking-tight">Registrasi Pasien Baru</h1>
          <p class="text-[11px] text-base-content/60">Lengkapi data untuk pendaftaran berobat pertama kali</p>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="handleRegister" class="p-5 space-y-3">

          <!-- NIK KTP -->
          <div class="space-y-1">
            <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 flex items-center justify-between">
              <span>NIK KTP (16 Digit)</span>
              <span class="text-error font-mono">*</span>
            </label>
            <input
              v-model="form.no_ktp"
              type="text"
              maxlength="16"
              placeholder="3302xxxxxxxxxxxx"
              class="input input-sm input-bordered w-full rounded-xl text-xs bg-base-200/50 font-mono focus:input-primary h-10"
              :class="{ 'input-error': form.errors.no_ktp }"
              required
            />
            <p v-if="form.errors.no_ktp" class="text-error text-[10px]">{{ form.errors.no_ktp }}</p>
          </div>

          <!-- Nama Lengkap -->
          <div class="space-y-1">
            <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 flex items-center justify-between">
              <span>Nama Lengkap (Sesuai KTP)</span>
              <span class="text-error font-mono">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Nama lengkap pasien"
              class="input input-sm input-bordered w-full rounded-xl text-xs bg-base-200/50 focus:input-primary h-10"
              :class="{ 'input-error': form.errors.name }"
              required
            />
            <p v-if="form.errors.name" class="text-error text-[10px]">{{ form.errors.name }}</p>
          </div>

          <!-- Jenis Kelamin & Tempat Lahir (Grid 2 Kolom) -->
          <div class="grid grid-cols-2 gap-2.5">
            <div class="space-y-1">
              <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60">Jenis Kelamin</label>
              <select
                v-model="form.jk"
                class="select select-sm select-bordered w-full rounded-xl text-xs bg-base-200/50 focus:select-primary h-10"
              >
                <option value="L">Laki-Laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>

            <div class="space-y-1">
              <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60">Tempat Lahir</label>
              <input
                v-model="form.tmp_lahir"
                type="text"
                placeholder="Kota lahir"
                class="input input-sm input-bordered w-full rounded-xl text-xs bg-base-200/50 focus:input-primary h-10"
                :class="{ 'input-error': form.errors.tmp_lahir }"
                required
              />
            </div>
          </div>

          <!-- Tanggal Lahir & No. WhatsApp (Grid 2 Kolom) -->
          <div class="grid grid-cols-2 gap-2.5">
            <div class="space-y-1">
              <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60">Tanggal Lahir</label>
              <input
                v-model="form.tgl_lahir"
                type="date"
                class="input input-sm input-bordered w-full rounded-xl text-xs bg-base-200/50 font-mono focus:input-primary h-10"
                :class="{ 'input-error': form.errors.tgl_lahir }"
                required
              />
            </div>

            <div class="space-y-1">
              <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60">No. WhatsApp</label>
              <input
                v-model="form.no_tlp"
                type="tel"
                placeholder="08xxxxxxxxxx"
                class="input input-sm input-bordered w-full rounded-xl text-xs bg-base-200/50 font-mono focus:input-primary h-10"
                :class="{ 'input-error': form.errors.no_tlp }"
                required
              />
            </div>
          </div>

          <!-- Nama Ibu Kandung -->
          <div class="space-y-1">
            <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60 flex items-center justify-between">
              <span>Nama Ibu Kandung</span>
              <span class="text-error font-mono">*</span>
            </label>
            <input
              v-model="form.nm_ibu"
              type="text"
              placeholder="Nama ibu kandung untuk verifikasi SIMRS"
              class="input input-sm input-bordered w-full rounded-xl text-xs bg-base-200/50 focus:input-primary h-10"
              :class="{ 'input-error': form.errors.nm_ibu }"
              required
            />
          </div>

          <!-- Alamat Lengkap -->
          <div class="space-y-1">
            <label class="text-[11px] font-bold uppercase tracking-wider text-base-content/60">Alamat Domisili</label>
            <textarea
              v-model="form.alamat"
              rows="2"
              placeholder="RT/RW, Desa/Kelurahan, Kecamatan, Kota/Kabupaten"
              class="textarea textarea-sm textarea-bordered w-full rounded-xl text-xs bg-base-200/50 focus:textarea-primary"
              :class="{ 'textarea-error': form.errors.alamat }"
              required
            ></textarea>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="btn btn-primary btn-block rounded-xl font-bold text-xs shadow-md shadow-primary/20 h-11 text-white mt-2"
            :disabled="form.processing"
          >
            <span v-if="form.processing" class="loading loading-spinner loading-xs"></span>
            <span>{{ form.processing ? 'Menyimpan Data...' : 'Konfirmasi Pendaftaran' }}</span>
          </button>

          <!-- Link Login -->
          <div class="text-center pt-2 border-t border-base-200">
            <p class="text-xs text-base-content/60">
              Sudah pernah berobat sebelumnya?
              <Link href="/" class="text-primary font-bold hover:underline ml-1">
                Masuk di sini
              </Link>
            </p>
          </div>

        </form>

      </div>

      <!-- Footer Info -->
      <div class="text-center">
        <p class="text-[11px] text-base-content/50 font-medium">
          RSUD Prof. Dr. Soepardjo Roestam
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
