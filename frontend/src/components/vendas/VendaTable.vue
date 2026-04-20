<template>
  <div class="overflow-x-auto rounded-xl border border-gray-200/60">
    <table class="min-w-full divide-y divide-gray-200/60">
      <thead class="bg-gradient-to-r from-gray-50 to-gray-50/50">
        <tr>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cliente</th>
          <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Itens</th>
          <th class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
          <th class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Lucro</th>
          <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data</th>
          <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr v-if="vendas.length === 0">
          <td colspan="8" class="px-4 py-12 text-center">
            <div class="flex flex-col items-center gap-2">
              <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-sm text-gray-400 font-medium">Nenhuma venda registrada.</p>
            </div>
          </td>
        </tr>
        <tr
          v-for="venda in vendas"
          :key="venda.id"
          class="hover:bg-indigo-50/30 transition-colors duration-150"
        >
          <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap font-medium">{{ venda.id }}</td>
          <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap font-medium">{{ venda.cliente }}</td>
          <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
              {{ venda.itens?.length || 0 }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm text-right whitespace-nowrap font-bold text-indigo-700">
            R$ {{ Number(venda.total).toFixed(2) }}
          </td>
          <td class="px-4 py-3 text-sm text-right whitespace-nowrap font-bold" :class="Number(venda.lucro) >= 0 ? 'text-emerald-600' : 'text-red-600'">
            R$ {{ Number(venda.lucro).toFixed(2) }}
          </td>
          <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="venda.cancelada ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
            >
              {{ venda.cancelada ? 'Cancelada' : 'Ativa' }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
            {{ venda.created_at ? new Date(venda.created_at).toLocaleDateString('pt-BR') : '—' }}
          </td>
          <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
            <div class="inline-flex items-center gap-1">
              <button
                class="p-2 rounded-lg text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200"
                title="Ver detalhes"
                @click="openDetail(venda)"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
              <button
                v-if="!venda.cancelada"
                class="p-2 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200"
                title="Cancelar venda"
                @click="confirmCancel(venda.id)"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
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
        <div v-if="detailVenda" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="detailVenda = null"></div>
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
            appear
          >
            <div v-if="detailVenda" class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-5">
              <!-- Header -->
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-bold text-gray-900">Venda #{{ detailVenda.id }}</h3>
                  <p class="text-sm text-gray-500 mt-0.5">{{ detailVenda.created_at ? new Date(detailVenda.created_at).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' }) : '—' }}</p>
                </div>
                <button class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all" @click="detailVenda = null">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Info -->
              <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[140px] bg-gray-50 rounded-xl p-3">
                  <p class="text-xs text-gray-400 font-semibold uppercase">Cliente</p>
                  <p class="text-sm font-bold text-gray-800 mt-1">{{ detailVenda.cliente }}</p>
                </div>
                <div class="flex-1 min-w-[140px] bg-gray-50 rounded-xl p-3">
                  <p class="text-xs text-gray-400 font-semibold uppercase">Status</p>
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                    :class="detailVenda.cancelada ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
                  >
                    {{ detailVenda.cancelada ? 'Cancelada' : 'Ativa' }}
                  </span>
                </div>
              </div>

              <!-- Itens -->
              <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Itens ({{ detailVenda.itens?.length || 0 }})</p>
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
                      <tr v-for="item in detailVenda.itens" :key="item.id" class="text-sm">
                        <td class="px-4 py-2.5 text-gray-800 font-medium">{{ item.produto?.nome || '—' }}</td>
                        <td class="px-4 py-2.5 text-gray-700 text-right">{{ item.quantidade }}</td>
                        <td class="px-4 py-2.5 text-gray-700 text-right">R$ {{ Number(item.preco_unitario).toFixed(2) }}</td>
                        <td class="px-4 py-2.5 text-indigo-600 font-semibold text-right">R$ {{ (item.quantidade * Number(item.preco_unitario)).toFixed(2) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Totais -->
              <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <div>
                  <p class="text-xs text-gray-400 font-semibold uppercase">Lucro</p>
                  <p class="text-lg font-bold" :class="Number(detailVenda.lucro) >= 0 ? 'text-emerald-600' : 'text-red-600'">
                    R$ {{ Number(detailVenda.lucro).toFixed(2) }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-xs text-gray-400 font-semibold uppercase">Total</p>
                  <p class="text-lg font-bold text-indigo-700">R$ {{ Number(detailVenda.total).toFixed(2) }}</p>
                </div>
              </div>

              <!-- Fechar -->
              <button
                class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                @click="detailVenda = null"
              >
                Fechar
              </button>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal de Confirmação Cancelar -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="cancelId !== null" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="cancelId = null"></div>
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
            appear
          >
            <div v-if="cancelId !== null" class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 space-y-5">
              <div class="flex flex-col items-center text-center gap-3">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                  <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Cancelar Venda</h3>
                <p class="text-sm text-gray-500">Tem certeza que deseja cancelar esta venda? O estoque será revertido. Esta ação não pode ser desfeita.</p>
              </div>
              <div class="flex gap-3">
                <button
                  class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                  @click="cancelId = null"
                >
                  Não, voltar
                </button>
                <button
                  class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-red-500 to-rose-600 text-white hover:from-red-600 hover:to-rose-700 shadow-lg shadow-red-500/20 transition-all"
                  @click="doCancel"
                >
                  Sim, cancelar
                </button>
              </div>
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
  vendas: { type: Array, default: () => [] },
})

const emit = defineEmits(['cancelar'])

const detailVenda = ref(null)
const cancelId = ref(null)

function openDetail(venda) {
  detailVenda.value = venda
}

function confirmCancel(id) {
  cancelId.value = id
}

function doCancel() {
  emit('cancelar', cancelId.value)
  cancelId.value = null
}
</script>
