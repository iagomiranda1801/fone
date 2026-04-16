import { ref, computed } from 'vue'

export function useItemList() {
  const items = ref([])

  function adicionarItem(item = {}) {
    items.value.push({
      produto_id: item.produto_id || null,
      quantidade: item.quantidade || 1,
      preco_unitario: item.preco_unitario || 0,
    })
  }

  function removerItem(index) {
    items.value.splice(index, 1)
  }

  function limpar() {
    items.value = []
  }

  const total = computed(() =>
    items.value.reduce((sum, item) => sum + item.quantidade * item.preco_unitario, 0),
  )

  function toPayload() {
    return items.value.map((item) => ({
      id: item.produto_id,
      quantidade: item.quantidade,
      preco_unitario: item.preco_unitario,
    }))
  }

  return { items, adicionarItem, removerItem, limpar, total, toPayload }
}
