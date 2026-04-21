<template>
  <div class="overflow-x-auto rounded-2xl border border-gray-200/60 shadow-sm">
    <table class="min-w-full divide-y divide-gray-200/60">
      <thead class="bg-gradient-to-r from-gray-50 to-gray-50/50">
        <tr>
          <th
            v-for="col in columns"
            :key="col"
            class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap"
          >
            {{ col }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr v-if="loading">
          <td :colspan="columns.length" class="px-4 py-10 text-center text-sm text-gray-400">
            <div class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Carregando...
            </div>
          </td>
        </tr>
        <tr
          v-else
          v-for="(row, index) in rows"
          :key="index"
          class="hover:bg-indigo-50/30 transition-colors duration-150"
        >
          <slot name="row" :row="row" :index="index" />
        </tr>
      </tbody>
    </table>
    <div v-if="!loading && rows.length > 0" class="px-4 py-2 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
      <p class="text-xs text-gray-400">{{ rows.length }} registro{{ rows.length !== 1 ? 's' : '' }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})
</script>
