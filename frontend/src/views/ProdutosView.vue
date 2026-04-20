<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Produtos</h1>
        <p class="text-sm text-gray-500 mt-0.5">Cadastro e controle de estoque</p>
      </div>
      <div class="flex items-center gap-2 text-sm text-gray-500">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-semibold">
          {{ produtos.length }} produto{{ produtos.length !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <CenterToast
      :show="!!notification"
      :message="notification?.message"
      :type="notification?.type"
      @dismiss="dismiss"
    />

    <PageLoading :loading="loading" message="Carregando produtos..." />

    <template v-if="!loading">
      <BaseCard title="Novo Produto">
        <ProdutoForm :loading="formLoading" @submit="handleCriar" />
      </BaseCard>

      <BaseCard title="Produtos Cadastrados">
        <template #header-extra>
          <SearchFilter
            v-model:search="searchProduto"
            placeholder="Buscar por nome..."
            :filtered="produtosFiltrados.length"
            :total="produtos.length"
          >
            <template #filters>
              <FilterChip :active="statusFilter === 'todos'" @click="statusFilter = 'todos'">Todos</FilterChip>
              <FilterChip :active="statusFilter === 'ativos'" @click="statusFilter = 'ativos'">Ativos</FilterChip>
              <FilterChip :active="statusFilter === 'inativos'" @click="statusFilter = 'inativos'">Inativos</FilterChip>
              <FilterChip :active="statusFilter === 'sem-estoque'" @click="statusFilter = 'sem-estoque'">Sem estoque</FilterChip>
            </template>
          </SearchFilter>
        </template>
        <ProdutoTable
          ref="produtoTableRef"
          :produtos="produtosFiltrados"
          @edit-nome="handleEditNome"
          @toggle="handleToggle"
        />
      </BaseCard>
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useProdutos } from '../composables/useProdutos'
import { useNotification } from '../composables/useNotification'
import CenterToast from '../components/ui/CenterToast.vue'
import PageLoading from '../components/ui/PageLoading.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import SearchFilter from '../components/ui/SearchFilter.vue'
import FilterChip from '../components/ui/FilterChip.vue'
import ProdutoForm from '../components/produtos/ProdutoForm.vue'
import ProdutoTable from '../components/produtos/ProdutoTable.vue'

const { produtos, loading, carregar, criar, editarNome, toggleAtivo } = useProdutos()
const { notification, notifySuccess, notifyError, dismiss } = useNotification()
const formLoading = ref(false)
const produtoTableRef = ref(null)
const searchProduto = ref('')
const statusFilter = ref('todos')

const produtosFiltrados = computed(() => {
  let list = produtos.value
  const term = searchProduto.value.toLowerCase().trim()
  if (term) {
    list = list.filter((p) => p.nome.toLowerCase().includes(term))
  }
  if (statusFilter.value === 'ativos') list = list.filter((p) => p.ativo)
  else if (statusFilter.value === 'inativos') list = list.filter((p) => !p.ativo)
  else if (statusFilter.value === 'sem-estoque') list = list.filter((p) => p.estoque === 0)
  return list
})

onMounted(carregar)

async function handleCriar(dados) {
  formLoading.value = true
  try {
    await criar(dados)
    notifySuccess('Produto cadastrado com sucesso!')
  } catch (err) {
    notifyError(typeof err.message === 'string' ? err.message : 'Erro ao cadastrar produto.')
  } finally {
    formLoading.value = false
  }
}

async function handleEditNome({ id, nome }) {
  try {
    await editarNome(id, nome)
    produtoTableRef.value?.finishEdit()
    notifySuccess('Nome do produto atualizado!')
  } catch (err) {
    produtoTableRef.value?.finishEdit()
    notifyError('Erro ao atualizar nome do produto.')
  }
}

async function handleToggle(id) {
  try {
    const produto = await toggleAtivo(id)
    notifySuccess(produto.ativo ? 'Produto ativado!' : 'Produto desativado!')
  } catch (err) {
    notifyError('Erro ao alterar status do produto.')
  }
}
</script>
