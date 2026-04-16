<template>
  <div class="space-y-8">
    <h1 class="text-2xl font-bold text-gray-800">Produtos</h1>

    <BaseAlert
      :show="!!notification"
      :message="notification?.message"
      :type="notification?.type"
      @dismiss="dismiss"
    />

    <BaseCard title="Novo Produto">
      <ProdutoForm :loading="loading" @submit="handleCriar" />
    </BaseCard>

    <BaseCard title="Produtos Cadastrados">
      <ProdutoTable :produtos="produtos" />
    </BaseCard>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useProdutos } from '../composables/useProdutos'
import { useNotification } from '../composables/useNotification'
import BaseAlert from '../components/ui/BaseAlert.vue'
import BaseCard from '../components/ui/BaseCard.vue'
import ProdutoForm from '../components/produtos/ProdutoForm.vue'
import ProdutoTable from '../components/produtos/ProdutoTable.vue'

const { produtos, loading, carregar, criar } = useProdutos()
const { notification, notifySuccess, notifyError, dismiss } = useNotification()
const formLoading = ref(false)

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
</script>
