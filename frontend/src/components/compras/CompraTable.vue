<template>
  <div class="overflow-x-auto rounded-xl border border-gray-200/60">
    <table class="min-w-full divide-y divide-gray-200/60">
      <thead class="bg-gradient-to-r from-gray-50 to-gray-50/50">
        <tr>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fornecedor</th>
          <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Itens</th>
          <th class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data</th>
          <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr v-if="compras.length === 0">
          <td colspan="6" class="px-4 py-12 text-center">
            <div class="flex flex-col items-center gap-2">
              <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-sm text-gray-400 font-medium">Nenhuma compra registrada.</p>
            </div>
          </td>
        </tr>
        <tr
          v-for="compra in compras"
          :key="compra.id"
          class="hover:bg-indigo-50/30 transition-colors duration-150"
        >
          <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap font-medium">{{ compra.id }}</td>
          <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap font-medium">{{ compra.fornecedor }}</td>
          <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
              {{ compra.itens?.length || 0 }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm text-right whitespace-nowrap font-bold text-indigo-700">
            R$ {{ compraTotal(compra) }}
          </td>
          <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
            {{ compra.created_at ? new Date(compra.created_at).toLocaleDateString('pt-BR') : '—' }}
          </td>
          <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
            <button
              class="p-2 rounded-lg text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200"
              title="Ver detalhes"
              @click="openDetail(compra)"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal de Detalhes -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="detailCompra" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="detailCompra = null"></div>
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
            appear
          >
            <div v-if="detailCompra" class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-5">
              <!-- Header -->
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-bold text-gray-900">Compra #{{ detailCompra.id }}</h3>
                  <p class="text-sm text-gray-500 mt-0.5">{{ detailCompra.created_at ? new Date(detailCompra.created_at).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' }) : '—' }}</p>
                </div>
                <button class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all" @click="detailCompra = null">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Info -->
              <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs text-gray-400 font-semibold uppercase">Fornecedor</p>
                <p class="text-sm font-bold text-gray-800 mt-1">{{ detailCompra.fornecedor }}</p>
              </div>

              <!-- Itens -->
              <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Itens ({{ detailCompra.itens?.length || 0 }})</p>
                <div class="rounded-xl border border-gray-200/60 overflow-hidden">
                  <table class="min-w-full">
                    <thead class="bg-gray-50">
                      <tr class="text-xs text-gray-500 uppercase">
                        <th class="px-4 py-2.5 text-left font-semibold">Produto</th>
                        <th class="px-4 py-2.5 text-right font-semibold">Qtd</th>
                        <th class="px-4 py-2.5 text-right font-semibold">Preço Unit.</th>
                        <th class="px-4 py-2.5 text-right font-semibold">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                      <tr v-for="item in detailCompra.itens" :key="item.id" class="text-sm">
                        <td class="px-4 py-2.5 text-gray-800 font-medium">{{ item.produto?.nome || '—' }}</td>
                        <td class="px-4 py-2.5 text-gray-700 text-right">{{ item.quantidade }}</td>
                        <td class="px-4 py-2.5 text-gray-700 text-right">R$ {{ Number(item.preco_unitario).toFixed(2) }}</td>
                        <td class="px-4 py-2.5 text-indigo-600 font-semibold text-right">R$ {{ (item.quantidade * Number(item.preco_unitario)).toFixed(2) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Total -->
              <div class="flex items-center justify-end pt-2 border-t border-gray-100">
                <div class="text-right">
                  <p class="text-xs text-gray-400 font-semibold uppercase">Total</p>
                  <p class="text-lg font-bold text-indigo-700">R$ {{ compraTotal(detailCompra) }}</p>
                </div>
              </div>

              <!-- Fechar -->
              <button
                class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                @click="detailCompra = null"
              >
                Fechar
              </button>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  compras: { type: Array, default: () => [] },
})

const detailCompra = ref(null)

function openDetail(compra) {
  detailCompra.value = compra
}

function compraTotal(compra) {
  return compra.itens.reduce((s, i) => s + i.quantidade * Number(i.preco_unitario), 0).toFixed(2)
}
</script>
