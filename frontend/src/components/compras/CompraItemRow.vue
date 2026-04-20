<template>
  <div class="space-y-2">
    <div class="flex items-end gap-3">
      <BaseSelect
        v-model="item.produto_id"
        label="Produto"
        :options="produtoOptions"
        placeholder="Selecione um produto"
        class="flex-1"
        @update:model-value="onProdutoChange"
      />
      <BaseInput
        v-model="item.quantidade"
        label="Quantidade"
        type="number"
        min="1"
        class="w-28"
      />
      <BaseInput
        v-model="item.preco_unitario"
        label="Preço Unitário (R$)"
        type="number"
        step="0.01"
        min="0.01"
        class="w-36"
      />
      <BaseButton variant="danger" @click="$emit('remover')">
        ✕
      </BaseButton>
    </div>

    <!-- Dicas inteligentes -->
    <div v-if="produto" class="flex flex-wrap gap-2 pl-1">
      <!-- Preço ideal para 50% de lucro -->
      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Para 50% de lucro, compre por até <strong>R$ {{ precoIdeal }}</strong>
      </span>

      <!-- Histórico de preços -->
      <span v-if="produto.menor_preco_compra !== null" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
        </svg>
        Menor: R$ {{ Number(produto.menor_preco_compra).toFixed(2) }} | Maior: R$ {{ Number(produto.maior_preco_compra).toFixed(2) }}
      </span>

      <!-- Alerta se preço atual é mais caro que histórico -->
      <span
        v-if="precoAlerta === 'caro'"
        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        Preço acima do histórico!
      </span>
      <span
        v-else-if="precoAlerta === 'barato'"
        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Preço abaixo do histórico!
      </span>

      <!-- Alerta se lucro será menor que 50% -->
      <span
        v-if="item.preco_unitario && Number(item.preco_unitario) > Number(precoIdeal)"
        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Lucro será menor que 50%!
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BaseSelect from '../ui/BaseSelect.vue'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'

const props = defineProps({
  item: { type: Object, required: true },
  produtoOptions: { type: Array, default: () => [] },
  produtosMap: { type: Object, default: () => ({}) },
})

defineEmits(['remover'])

const produto = computed(() => props.produtosMap[props.item.produto_id] || null)

const precoIdeal = computed(() => {
  if (!produto.value) return '0.00'
  return (produto.value.preco_venda / 1.5).toFixed(2)
})

const precoAlerta = computed(() => {
  if (!produto.value || !props.item.preco_unitario || produto.value.menor_preco_compra === null) return null
  const preco = Number(props.item.preco_unitario)
  if (preco > Number(produto.value.maior_preco_compra)) return 'caro'
  if (preco < Number(produto.value.menor_preco_compra)) return 'barato'
  return null
})

function onProdutoChange(produtoId) {
  const p = props.produtosMap[produtoId]
  if (p && p.custo_medio > 0) {
    props.item.preco_unitario = p.custo_medio
  }
}
</script>
