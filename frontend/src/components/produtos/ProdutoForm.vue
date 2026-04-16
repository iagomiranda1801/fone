<template>
  <form @submit.prevent="onSubmit" class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <BaseInput
        v-model="form.nome"
        label="Nome do Produto"
        placeholder="Mínimo 3 caracteres"
        :error="errors.nome"
      />
      <BaseInput
        v-model="form.preco_venda"
        label="Preço de Venda (R$)"
        type="number"
        step="0.01"
        min="0.01"
        placeholder="0.00"
        :error="errors.preco_venda"
      />
    </div>
    <BaseButton type="submit" :loading="loading">
      Cadastrar Produto
    </BaseButton>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'

const emit = defineEmits(['submit'])
const props = defineProps({
  loading: { type: Boolean, default: false },
})

const form = reactive({ nome: '', preco_venda: '' })
const errors = reactive({ nome: '', preco_venda: '' })

function validate() {
  errors.nome = ''
  errors.preco_venda = ''

  if (!form.nome || form.nome.length < 3) {
    errors.nome = 'O nome deve ter no mínimo 3 caracteres.'
  }

  const preco = Number(form.preco_venda)
  if (!form.preco_venda || preco <= 0) {
    errors.preco_venda = 'O preço deve ser maior que zero.'
  }

  return !errors.nome && !errors.preco_venda
}

function onSubmit() {
  if (!validate()) return

  emit('submit', {
    nome: form.nome,
    preco_venda: Number(form.preco_venda),
  })

  form.nome = ''
  form.preco_venda = ''
}
</script>
