<script setup>
defineProps({
  isOnline: {
    type: Boolean,
    default: true
  }
})

defineEmits(['toggle-drawer'])
</script>

<template>
  <header class="sticky top-0 z-40 bg-base-200/85 backdrop-blur-md transition-all">
    <div class="max-w-md mx-auto px-4 py-2.5 flex items-center justify-between">
      
      <!-- Brand & Drawer Trigger Area -->
      <div class="flex items-center gap-2">
        <!-- Hamburger Menu Button -->
        <button 
          @click="$emit('toggle-drawer')" 
          class="btn btn-ghost btn-circle btn-sm bg-base-100 border border-base-300/60 shadow-2xs text-base-content/80 hover:text-base-content"
          aria-label="Buka Menu Sidebar"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
          </svg>
        </button>

        <div class="w-8 h-8 rounded-xl bg-base-100 p-1.5 border border-base-300/60 shadow-2xs flex items-center justify-center">
          <img 
            src="/icon_rsmd.png" 
            alt="Logo RSMD" 
            class="w-full h-full object-contain" 
          />
        </div>
        
        <div class="flex flex-col">
          <span class="text-sm font-black tracking-tight text-base-content leading-none">
            RSMD<span class="text-primary font-bold">Mobile</span>
          </span>
          <span class="text-[10px] text-base-content/50 font-medium tracking-wide mt-0.5">
            Layanan Digital
          </span>
        </div>
      </div>
      
      <!-- Status & Notification Actions -->
      <div class="flex items-center gap-1.5">
        
        <!-- Live Connection Pill -->
        <div 
          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-semibold bg-base-100/90 shadow-2xs"
          :class="isOnline ? 'border-base-300 text-base-content/70' : 'border-error/30 text-error animate-pulse'"
        >
          <span class="relative flex h-1.5 w-1.5">
            <span 
              v-if="isOnline"
              class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"
            ></span>
            <span 
              class="relative inline-flex rounded-full h-1.5 w-1.5"
              :class="isOnline ? 'bg-success' : 'bg-error'"
            ></span>
          </span>
          <span>{{ isOnline ? 'Online' : 'Offline' }}</span>
        </div>

        <!-- Notification Button -->
        <div class="relative">
          <button 
            class="btn btn-circle btn-sm btn-ghost bg-base-100 border border-base-300/60 shadow-2xs text-base-content/70 hover:text-base-content" 
            aria-label="Pemberitahuan"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </button>
          
          <span class="absolute 0 top-0.5 right-0.5 flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
          </span>
        </div>

      </div>

    </div>
  </header>
</template>