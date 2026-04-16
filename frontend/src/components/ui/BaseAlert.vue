<template>
  <Transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 translate-y-[-10px]"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-[-10px]"
  >
    <div
      v-if="show"
      class="rounded-md p-4 text-sm flex items-center justify-between"
      :class="typeClasses"
      role="alert"
    >
      <span>{{ message }}</span>
      <button
        v-if="dismissible"
        class="ml-4 text-current opacity-60 hover:opacity-100"
        @click="$emit('dismiss')"
      >
        &times;
      </button>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  message: { type: String, default: '' },
  type: { type: String, default: 'success' },
  show: { type: Boolean, default: false },
  dismissible: { type: Boolean, default: true },
})

defineEmits(['dismiss'])

const typeClasses = computed(() => {
  const types = {
    success: 'bg-emerald-50 text-emerald-800 border border-emerald-200',
    error: 'bg-red-50 text-red-800 border border-red-200',
    warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
    info: 'bg-blue-50 text-blue-800 border border-blue-200',
  }
  return types[props.type] || types.info
})
</script>
