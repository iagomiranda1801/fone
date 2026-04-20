import { ref } from 'vue'
import { getProdutos, criarProduto, updateNomeProduto, toggleAtivoProduto } from '../services/produtoService'

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

  async function editarNome(id, nome) {
    const { data } = await updateNomeProduto(id, nome)
    const index = produtos.value.findIndex((p) => p.id === id)
    if (index !== -1) produtos.value[index] = data.data
    return data.data
  }

  async function toggleAtivo(id) {
    const { data } = await toggleAtivoProduto(id)
    const index = produtos.value.findIndex((p) => p.id === id)
    if (index !== -1) produtos.value[index] = data.data
    return data.data
  }

  return { produtos, loading, carregar, criar, editarNome, toggleAtivo }
}
