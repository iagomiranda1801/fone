import { ref } from 'vue'
import { getProdutos, criarProduto } from '../services/produtoService'

export function useProdutos() {
  const produtos = ref([])
  const loading = ref(false)

  async function carregar() {
    loading.value = true
    try {
      const { data } = await getProdutos()
      produtos.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function criar(dados) {
    const { data } = await criarProduto(dados)
    produtos.value.push(data.data)
    return data.data
  }

  return { produtos, loading, carregar, criar }
}
