import { ref } from 'vue'
import { getCompras, registrarCompra } from '../services/compraService'

export function useCompras() {
  const compras = ref([])
  const loading = ref(false)

  async function carregar() {
    loading.value = true
    try {
      const { data } = await getCompras()
      compras.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function registrar(dados) {
    const { data } = await registrarCompra(dados)
    compras.value.unshift(data.data)
    return data.data
  }

  return { compras, loading, carregar, registrar }
}
