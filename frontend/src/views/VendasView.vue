<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Vendas</h1>
        <p class="text-sm text-gray-500 mt-0.5">Registre saídas e acompanhe o lucro</p>
      </div>
      <div class="flex items-center gap-2 text-sm text-gray-500">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 font-semibold">
          {{ vendas.length }} venda{{ vendas.length !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <CenterToast
      :show="!!notification"
      :message="notification?.message"
      :type="notification?.type"
      @dismiss="dismiss"
    />

    <PageLoading :loading="pageLoading" message="Carregando vendas..." />

    <template v-if="!pageLoading">
      <BaseCard title="Nova Venda">
        <VendaForm :produtos="produtos" :loading="formLoading" @submit="handleRegistrar" />
      </BaseCard>

      <BaseCard title="Histórico de Vendas">
        <template #header-extra>
          <SearchFilter
            v-model:search="searchVenda"
            placeholder="Buscar por cliente ou produto..."
            :filtered="vendasFiltradas.length"
            :total="vendas.length"
          >
            <template #filters>
              <FilterChip :active="statusVenda === 'todas'" @click="statusVenda = 'todas'">Todas</FilterChip>
              <FilterChip :active="statusVenda === 'ativas'" @click="statusVenda = 'ativas'">Ativas</FilterChip>
              <FilterChip :active="statusVenda === 'canceladas'" @click="statusVenda = 'canceladas'">Canceladas</FilterChip>
            </template>
          </SearchFilter>
        </template>
        <VendaTable :vendas="vendasFiltradas" @cancelar="handleCancelar" />
      </BaseCard>
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useProdutos } from '../composables/useProdutos'
import { useVendas } from '../composables/useVendas'
import { useNotification } from '../composables/useNotification'
import CenterToast from '../components/ui/CenterToast.vue'
import PageLoading from '../components/ui/PageLoading.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import SearchFilter from '../components/ui/SearchFilter.vue'
import FilterChip from '../components/ui/FilterChip.vue'
import VendaForm from '../components/vendas/VendaForm.vue'
import VendaTable from '../components/vendas/VendaTable.vue'

const { produtos, carregar: carregarProdutos } = useProdutos()
const { vendas, carregar: carregarVendas, registrar, cancelar } = useVendas()
const { notification, notifySuccess, notifyError, dismiss } = useNotification()
const formLoading = ref(false)
const pageLoading = ref(true)
const searchVenda = ref('')
const statusVenda = ref('todas')

const vendasFiltradas = computed(() => {
  let list = vendas.value
  const term = searchVenda.value.toLowerCase().trim()
  if (term) {
    list = list.filter((v) =>
      v.cliente.toLowerCase().includes(term) ||
      v.itens?.some((i) => i.produto?.nome?.toLowerCase().includes(term))
    )
  }
  if (statusVenda.value === 'ativas') list = list.filter((v) => !v.cancelada)
  else if (statusVenda.value === 'canceladas') list = list.filter((v) => v.cancelada)
  return list
})

onMounted(async () => {
  try {
    await Promise.all([carregarProdutos(), carregarVendas()])
  } finally {
    pageLoading.value = false
  }
})

async function handleRegistrar(dados) {
  formLoading.value = true
  try {
    const venda = await registrar(dados)
    await carregarProdutos()
    notifySuccess(`Venda registrada! Total: R$ ${venda.total.toFixed(2)} | Lucro: R$ ${venda.lucro.toFixed(2)}`)
  } catch (err) {
    notifyError(typeof err.message === 'string' ? err.message : 'Erro ao registrar venda.')
  } finally {
    formLoading.value = false
  }
}

async function handleCancelar(id) {
  try {
    await cancelar(id)
    await carregarProdutos()
    notifySuccess('Venda cancelada. Estoque revertido.')
  } catch (err) {
    notifyError(typeof err.message === 'string' ? err.message : 'Erro ao cancelar venda.')
  }
}
</script>
