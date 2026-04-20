<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="loading" class="flex flex-col items-center justify-center py-20 gap-4">
      <div class="relative">
        <div class="w-12 h-12 rounded-full border-4 border-gray-200"></div>
        <div class="absolute inset-0 w-12 h-12 rounded-full border-4 border-transparent border-t-indigo-500 animate-spin"></div>
      </div>
      <p class="text-sm font-medium text-gray-400">{{ message }}</p>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  loading: { type: Boolean, default: false },
  message: { type: String, default: 'Carregando...' },
})
</script>
