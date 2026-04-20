<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-400"
      enter-from-class="opacity-0 scale-95 translate-y-4"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition ease-in duration-300"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-4"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-[100] flex items-start justify-center pointer-events-none pt-8 px-4"
      >
        <div
          class="pointer-events-auto max-w-md w-full rounded-2xl shadow-2xl border p-5 flex items-start gap-4 backdrop-blur-sm"
          :class="typeStyles.container"
        >
          <!-- Icon -->
          <div
            class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center"
            :class="typeStyles.icon"
          >
            <svg v-if="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg v-else-if="type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <!-- Content -->
          <div class="flex-1 min-w-0 pt-0.5">
            <p class="text-sm font-semibold" :class="typeStyles.title">{{ typeTitle }}</p>
            <p class="text-sm mt-0.5" :class="typeStyles.message">{{ message }}</p>
          </div>
          <!-- Close -->
          <button
            @click="$emit('dismiss')"
            class="flex-shrink-0 p-1 rounded-lg transition-colors"
            :class="typeStyles.close"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  message: { type: String, default: '' },
  type: { type: String, default: 'success' },
})

defineEmits(['dismiss'])

const typeTitle = computed(() => {
  const titles = {
    success: 'Sucesso!',
    error: 'Erro!',
    warning: 'Atenção!',
    info: 'Informação',
  }
  return titles[props.type] || titles.info
})

const typeStyles = computed(() => {
  const styles = {
    success: {
      container: 'bg-white/95 border-emerald-200 shadow-emerald-500/10',
      icon: 'bg-emerald-100 text-emerald-600',
      title: 'text-emerald-800',
      message: 'text-emerald-700/80',
      close: 'text-emerald-400 hover:text-emerald-600 hover:bg-emerald-50',
    },
    error: {
      container: 'bg-white/95 border-red-200 shadow-red-500/10',
      icon: 'bg-red-100 text-red-600',
      title: 'text-red-800',
      message: 'text-red-700/80',
      close: 'text-red-400 hover:text-red-600 hover:bg-red-50',
    },
    warning: {
      container: 'bg-white/95 border-amber-200 shadow-amber-500/10',
      icon: 'bg-amber-100 text-amber-600',
      title: 'text-amber-800',
      message: 'text-amber-700/80',
      close: 'text-amber-400 hover:text-amber-600 hover:bg-amber-50',
    },
    info: {
      container: 'bg-white/95 border-blue-200 shadow-blue-500/10',
      icon: 'bg-blue-100 text-blue-600',
      title: 'text-blue-800',
      message: 'text-blue-700/80',
      close: 'text-blue-400 hover:text-blue-600 hover:bg-blue-50',
    },
  }
  return styles[props.type] || styles.info
})
</script>
