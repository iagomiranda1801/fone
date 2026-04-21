<template>
  <div class="relative overflow-hidden rounded-2xl p-4 bg-white border border-gray-200/60 shadow-sm">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ value }}</p>
        <p class="text-xs text-gray-400 font-medium mt-0.5">{{ label }}</p>
      </div>
      <div class="flex items-center justify-center w-10 h-10 rounded-xl shadow-md" :class="iconBg">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="icon"></svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: String,
  value: [String, Number],
  color: { type: String, default: 'indigo' },
  icon: String,
})

const iconBg = computed(() => ({
  indigo: 'bg-gradient-to-br from-indigo-500 to-purple-600 shadow-indigo-500/25',
  blue:   'bg-gradient-to-br from-blue-500 to-cyan-600 shadow-blue-500/25',
  green:  'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-emerald-500/25',
  teal:   'bg-gradient-to-br from-teal-500 to-emerald-600 shadow-teal-500/25',
  amber:  'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/25',
  orange: 'bg-gradient-to-br from-orange-500 to-red-500 shadow-orange-500/25',
  red:    'bg-gradient-to-br from-red-500 to-rose-600 shadow-red-500/25',
  violet: 'bg-gradient-to-br from-violet-500 to-purple-600 shadow-violet-500/25',
}[props.color] || 'bg-gradient-to-br from-gray-500 to-gray-600'))
</script>
