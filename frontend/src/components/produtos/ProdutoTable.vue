<template>
  <div class="overflow-x-auto rounded-xl border border-gray-200/60">
    <table class="min-w-full divide-y divide-gray-200/60">
      <thead class="bg-gradient-to-r from-gray-50 to-gray-50/50">
        <tr>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nome</th>
          <th class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Custo Médio</th>
          <th class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Preço Venda</th>
          <th class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Estoque</th>
          <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr v-if="produtos.length === 0">
          <td colspan="7" class="px-4 py-12 text-center">
            <div class="flex flex-col items-center gap-2">
              <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <p class="text-sm text-gray-400 font-medium">Nenhum produto cadastrado.</p>
            </div>
          </td>
        </tr>
        <tr
          v-for="produto in produtos"
          :key="produto.id"
          class="hover:bg-indigo-50/30 transition-colors duration-150"
          :class="{ 'opacity-50': !produto.ativo }"
        >
          <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap font-medium">{{ produto.id }}</td>
          <td class="px-4 py-3 text-sm text-gray-800 whitespace-nowrap font-medium">
            {{ produto.nome }}
          </td>
          <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">R$ {{ Number(produto.custo_medio).toFixed(2) }}</td>
          <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">R$ {{ Number(produto.preco_venda).toFixed(2) }}</td>
          <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap font-semibold">{{ produto.estoque }}</td>
          <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="produto.ativo ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
            >
              {{ produto.ativo ? 'Ativo' : 'Inativo' }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
            <div class="inline-flex items-center gap-1">
              <!-- Editar nome -->
              <button
                class="p-2 rounded-lg text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200"
                title="Editar nome"
                @click="openEdit(produto)"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <!-- Toggle ativo -->
              <button
                class="p-2 rounded-lg transition-all duration-200"
                :class="produto.ativo
                  ? 'text-red-400 hover:text-red-600 hover:bg-red-50'
                  : 'text-emerald-400 hover:text-emerald-600 hover:bg-emerald-50'"
                :title="produto.ativo ? 'Desativar produto' : 'Ativar produto'"
                @click="confirmToggle(produto)"
              >
                <svg v-if="produto.ativo" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal Editar Nome -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="editProduto" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeEdit"></div>
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
            appear
          >
            <div v-if="editProduto" class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 space-y-5">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Editar Produto</h3>
                <button class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all" @click="closeEdit">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                  <input
                    v-model="editNome"
                    type="text"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all"
                    placeholder="Nome do produto"
                    ref="editInput"
                  />
                  <p v-if="editError" class="text-xs text-red-500 mt-1">{{ editError }}</p>
                </div>
                <div class="flex gap-3">
                  <button
                    type="button"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                    @click="closeEdit"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-500/20 transition-all disabled:opacity-50"
                    :disabled="editSaving"
                  >
                    <span v-if="editSaving" class="inline-flex items-center gap-2">
                      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                      </svg>
                      Salvando...
                    </span>
                    <span v-else>Salvar</span>
                  </button>
                </div>
              </form>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

    <!-- Modal Confirmar Toggle -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="toggleProduto" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="toggleProduto = null"></div>
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
            appear
          >
            <div v-if="toggleProduto" class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 space-y-5">
              <div class="flex flex-col items-center text-center gap-3">
                <div
                  class="w-14 h-14 rounded-full flex items-center justify-center"
                  :class="toggleProduto.ativo ? 'bg-red-100' : 'bg-emerald-100'"
                >
                  <svg v-if="toggleProduto.ativo" class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                  </svg>
                  <svg v-else class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">
                  {{ toggleProduto.ativo ? 'Desativar Produto' : 'Ativar Produto' }}
                </h3>
                <p class="text-sm text-gray-500">
                  {{ toggleProduto.ativo
                    ? `Deseja desativar "${toggleProduto.nome}"? Ele não aparecerá nas opções de compra e venda.`
                    : `Deseja reativar "${toggleProduto.nome}"? Ele voltará a aparecer nas opções de compra e venda.`
                  }}
                </p>
              </div>
              <div class="flex gap-3">
                <button
                  class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                  @click="toggleProduto = null"
                >
                  Cancelar
                </button>
                <button
                  class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-lg transition-all"
                  :class="toggleProduto.ativo
                    ? 'bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 shadow-red-500/20'
                    : 'bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 shadow-emerald-500/20'"
                  @click="doToggle"
                >
                  {{ toggleProduto.ativo ? 'Sim, desativar' : 'Sim, ativar' }}
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
import { ref, nextTick } from 'vue'

const props = defineProps({
  produtos: { type: Array, default: () => [] },
})

const emit = defineEmits(['edit-nome', 'toggle'])

const editProduto = ref(null)
const editNome = ref('')
const editError = ref('')
const editSaving = ref(false)
const editInput = ref(null)
const toggleProduto = ref(null)

function confirmToggle(produto) {
  toggleProduto.value = produto
}

function doToggle() {
  emit('toggle', toggleProduto.value.id)
  toggleProduto.value = null
}

function openEdit(produto) {
  editProduto.value = produto
  editNome.value = produto.nome
  editError.value = ''
  nextTick(() => editInput.value?.focus())
}

function closeEdit() {
  editProduto.value = null
  editNome.value = ''
  editError.value = ''
}

function submitEdit() {
  editError.value = ''
  if (!editNome.value || editNome.value.trim().length < 3) {
    editError.value = 'O nome deve ter no mínimo 3 caracteres.'
    return
  }
  editSaving.value = true
  emit('edit-nome', { id: editProduto.value.id, nome: editNome.value.trim() })
}

function finishEdit() {
  editSaving.value = false
  closeEdit()
}

defineExpose({ finishEdit })
</script>
