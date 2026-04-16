import { ref } from 'vue'
import { getVendas, registrarVenda, cancelarVenda } from '../services/vendaService'

export function useVendas() {
  const vendas = ref([])
  const loading = ref(false)

  async function carregar() {
    loading.value = true
    try {
      const { data } = await getVendas()
      vendas.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function registrar(dados) {
    const { data } = await registrarVenda(dados)
    vendas.value.unshift(data.data)
    return data.data
  }

  async function cancelar(id) {
    const { data } = await cancelarVenda(id)
    const index = vendas.value.findIndex((v) => v.id === id)
    if (index !== -1) vendas.value[index] = data.data
    return data.data
  }

  return { vendas, loading, carregar, registrar, cancelar }
}
