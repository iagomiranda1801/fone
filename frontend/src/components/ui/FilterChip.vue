<template>
  <button
    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold border transition-all duration-200 whitespace-nowrap"
    :class="active
      ? 'border-indigo-300 bg-indigo-50 text-indigo-700 shadow-sm'
      : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300 hover:bg-gray-50'"
    @click="$emit('click')"
  >
    <slot />
  </button>
</template>

<script setup>
defineProps({
  active: { type: Boolean, default: false },
})

defineEmits(['click'])
</script>
