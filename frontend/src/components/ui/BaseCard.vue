<template>
  <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm hover:shadow-md transition-shadow duration-300">
    <div v-if="title" class="px-6 py-4 border-b border-gray-100">
      <div class="flex items-center gap-3">
        <div class="w-1 h-6 rounded-full bg-gradient-to-b from-indigo-500 to-purple-500"></div>
        <h3 class="text-base font-semibold text-gray-800">{{ title }}</h3>
      </div>
      <div v-if="$slots['header-extra']" class="mt-4">
        <slot name="header-extra" />
      </div>
    </div>
    <div class="p-6">
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: '' },
})
</script>
