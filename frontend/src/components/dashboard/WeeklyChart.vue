<template>
  <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-2">
        <div class="w-1 h-6 rounded-full bg-gradient-to-b from-indigo-500 to-purple-500"></div>
        <h3 class="text-sm font-bold text-gray-800">Últimos 7 dias</h3>
      </div>
      <div class="flex items-center gap-3 text-[11px] font-semibold">
        <span class="flex items-center gap-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"></span>
          Faturamento
        </span>
        <span class="flex items-center gap-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"></span>
          Lucro
        </span>
        <span class="flex items-center gap-1.5">
          <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-r from-amber-500 to-orange-500"></span>
          Compras
        </span>
      </div>
    </div>

    <div class="relative h-52">
      <!-- Grid lines -->
      <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
        <div v-for="i in 5" :key="i" class="border-b border-dashed border-gray-100 w-full"></div>
      </div>

      <!-- Y axis labels -->
      <div class="absolute -left-1 inset-y-0 flex flex-col justify-between text-[10px] text-gray-300 font-medium">
        <span>{{ money(maxVal) }}</span>
        <span>{{ money(maxVal * 0.75) }}</span>
        <span>{{ money(maxVal * 0.5) }}</span>
        <span>{{ money(maxVal * 0.25) }}</span>
        <span>R$ 0</span>
      </div>

      <!-- Bars -->
      <div class="absolute inset-0 pl-14 pr-2 flex items-end gap-2">
        <div
          v-for="(dia, idx) in dias"
          :key="idx"
          class="flex-1 flex flex-col items-center gap-1 group"
        >
          <div class="relative w-full flex items-end justify-center gap-[3px]" style="height: 180px;">
            <!-- Faturamento bar -->
            <div
              class="w-full max-w-[18px] rounded-t-md bg-gradient-to-t from-indigo-500 to-purple-400 opacity-90 transition-all duration-500 group-hover:opacity-100 relative"
              :style="{ height: barHeight(dia.faturamento) }"
            >
              <div class="absolute -top-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded-md whitespace-nowrap font-medium shadow-lg z-10">
                {{ money(dia.faturamento) }}
              </div>
            </div>
            <!-- Lucro bar -->
            <div
              class="w-full max-w-[18px] rounded-t-md bg-gradient-to-t from-emerald-500 to-teal-400 opacity-90 transition-all duration-500 group-hover:opacity-100"
              :style="{ height: barHeight(dia.lucro) }"
            ></div>
            <!-- Compras indicator -->
            <div
              v-if="dia.compras > 0"
              class="absolute -top-1 right-0 w-3 h-3 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 border-2 border-white shadow-sm"
              :title="`${dia.compras} compra(s)`"
            ></div>
          </div>
          <span class="text-[10px] text-gray-400 font-medium">{{ formatDay(dia.data) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  dias: { type: Array, required: true },
})

function money(val) {
  if (val >= 1000) return `R$ ${(val / 1000).toFixed(1)}k`
  return `R$ ${Number(val).toFixed(0)}`
}

function formatDay(dateStr) {
  const d = new Date(dateStr + 'T12:00:00')
  return d.toLocaleDateString('pt-BR', { weekday: 'short' }).replace('.', '')
}

const maxVal = computed(() => {
  const vals = props.dias.flatMap(d => [d.faturamento, d.lucro])
  return Math.max(...vals, 1) * 1.1
})

function barHeight(val) {
  const pct = maxVal.value > 0 ? (val / maxVal.value) * 100 : 0
  return `${Math.max(pct, 1)}%`
}
</script>
