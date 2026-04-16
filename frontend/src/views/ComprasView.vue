<template>
  <div class="space-y-8">
    <h1 class="text-2xl font-bold text-gray-800">Compras</h1>

    <BaseAlert
      :show="!!notification"
      :message="notification?.message"
      :type="notification?.type"
      @dismiss="dismiss"
    />

    <BaseCard title="Nova Compra">
      <CompraForm :produtos="produtos" :loading="formLoading" @submit="handleRegistrar" />
    </BaseCard>

    <BaseCard title="Histórico de Compras">
      <CompraTable :compras="compras" />
    </BaseCard>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useProdutos } from '../composables/useProdutos'
import { useCompras } from '../composables/useCompras'
import { useNotification } from '../composables/useNotification'
import BaseAlert from '../components/ui/BaseAlert.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import CompraForm from '../components/compras/CompraForm.vue'
import CompraTable from '../components/compras/CompraTable.vue'

const { produtos, carregar: carregarProdutos } = useProdutos()
const { compras, carregar: carregarCompras, registrar } = useCompras()
const { notification, notifySuccess, notifyError, dismiss } = useNotification()
const formLoading = ref(false)

onMounted(async () => {
  await Promise.all([carregarProdutos(), carregarCompras()])
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
