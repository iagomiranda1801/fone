<template>
  <div class="min-h-screen bg-gray-50/50 flex">
    <!-- Sidebar -->
    <AppSidebar />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
      <!-- Top bar (mobile) -->
      <header class="lg:hidden sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-gray-200/60 px-4 py-3 flex items-center justify-between">
        <button @click="toggleMobile" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <span class="text-sm font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">ERP Estoque</span>
        <div class="w-9"></div>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <slot />
      </main>
    </div>

    <!-- Mobile overlay -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="mobileOpen"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
        @click="toggleMobile"
      ></div>
    </Transition>

    <!-- Mobile sidebar -->
    <Transition
      enter-active-class="transition ease-out duration-300 transform"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition ease-in duration-200 transform"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div v-if="mobileOpen" class="fixed inset-y-0 left-0 z-50 lg:hidden">
        <AppSidebar :mobile="true" @close="toggleMobile" />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, provide } from 'vue'
import AppSidebar from './AppSidebar.vue'

const mobileOpen = ref(false)

function toggleMobile() {
  mobileOpen.value = !mobileOpen.value
}

provide('toggleMobileSidebar', toggleMobile)
</script>
