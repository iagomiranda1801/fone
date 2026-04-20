<template>
  <div class="flex flex-wrap items-center gap-3">
    <!-- Search input -->
    <div class="relative w-80">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <input
        :value="search"
        @input="$emit('update:search', $event.target.value)"
        type="text"
        :placeholder="placeholder"
        class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all"
      />
      <button
        v-if="search"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
        @click="$emit('update:search', '')"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Filter chips -->
    <slot name="filters" />

    <!-- Results count -->
    <span v-if="total !== null" class="text-xs text-gray-400 whitespace-nowrap">
      {{ filtered }} de {{ total }}
    </span>
  </div>
</template>

<script setup>
defineProps({
  search: { type: String, default: '' },
  placeholder: { type: String, default: 'Pesquisar...' },
  filtered: { type: Number, default: null },
  total: { type: Number, default: null },
})

defineEmits(['update:search'])
</script>
