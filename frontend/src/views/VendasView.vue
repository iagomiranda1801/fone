<template>
  <div class="space-y-8">
    <h1 class="text-2xl font-bold text-gray-800">Vendas</h1>

    <BaseAlert
      :show="!!notification"
      :message="notification?.message"
      :type="notification?.type"
      @dismiss="dismiss"
    />

    <BaseCard title="Nova Venda">
      <VendaForm :produtos="produtos" :loading="formLoading" @submit="handleRegistrar" />
    </BaseCard>

    <BaseCard title="Histórico de Vendas">
      <VendaTable :vendas="vendas" @cancelar="handleCancelar" />
    </BaseCard>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useProdutos } from '../composables/useProdutos'
import { useVendas } from '../composables/useVendas'
import { useNotification } from '../composables/useNotification'
import BaseAlert from '../components/ui/BaseAlert.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import VendaForm from '../components/vendas/VendaForm.vue'
import VendaTable from '../components/vendas/VendaTable.vue'

const { produtos, carregar: carregarProdutos } = useProdutos()
const { vendas, carregar: carregarVendas, registrar, cancelar } = useVendas()
const { notification, notifySuccess, notifyError, dismiss } = useNotification()
const formLoading = ref(false)

onMounted(async () => {
  await Promise.all([carregarProdutos(), carregarVendas()])
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
