<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Compras</h1>
        <p class="text-sm text-gray-500 mt-0.5">Registre entradas de mercadorias</p>
      </div>
      <div class="flex items-center gap-2 text-sm text-gray-500">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold">
          {{ compras.length }} compra{{ compras.length !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <CenterToast
      :show="!!notification"
      :message="notification?.message"
      :type="notification?.type"
      @dismiss="dismiss"
    />

    <PageLoading :loading="pageLoading" message="Carregando compras..." />

    <template v-if="!pageLoading">
      <BaseCard title="Nova Compra">
        <CompraForm :produtos="produtos" :loading="formLoading" @submit="handleRegistrar" />
      </BaseCard>

      <BaseCard title="Histórico de Compras">
        <template #header-extra>
          <SearchFilter
            v-model:search="searchCompra"
            placeholder="Buscar por fornecedor ou produto..."
            :filtered="comprasFiltradas.length"
            :total="compras.length"
          />
        </template>
        <CompraTable :compras="comprasFiltradas" />
      </BaseCard>
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useProdutos } from '../composables/useProdutos'
import { useCompras } from '../composables/useCompras'
import { useNotification } from '../composables/useNotification'
import CenterToast from '../components/ui/CenterToast.vue'
import PageLoading from '../components/ui/PageLoading.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import SearchFilter from '../components/ui/SearchFilter.vue'
import CompraForm from '../components/compras/CompraForm.vue'
import CompraTable from '../components/compras/CompraTable.vue'

const { produtos, carregar: carregarProdutos } = useProdutos()
const { compras, carregar: carregarCompras, registrar } = useCompras()
const { notification, notifySuccess, notifyError, dismiss } = useNotification()
const formLoading = ref(false)
const pageLoading = ref(true)
const searchCompra = ref('')

const comprasFiltradas = computed(() => {
  const term = searchCompra.value.toLowerCase().trim()
  if (!term) return compras.value
  return compras.value.filter((c) =>
    c.fornecedor.toLowerCase().includes(term) ||
    c.itens?.some((i) => i.produto?.nome?.toLowerCase().includes(term))
  )
})

onMounted(async () => {
  try {
    await Promise.all([carregarProdutos(), carregarCompras()])
  } finally {
    pageLoading.value = false
  }
})

async function handleRegistrar(dados) {
  formLoading.value = true
  try {
    await registrar(dados)
    await carregarProdutos()
    notifySuccess('Compra registrada com sucesso! Estoque e custo médio atualizados.')
  } catch (err) {
    notifyError(typeof err.message === 'string' ? err.message : 'Erro ao registrar compra.')
  } finally {
    formLoading.value = false
  }
}
</script>
