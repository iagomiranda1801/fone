import api from './api'

export const getProdutos = () => api.get('/produtos')

export const criarProduto = (dados) => api.post('/produtos', dados)

export const updateNomeProduto = (id, nome) => api.patch(`/produtos/${id}/nome`, { nome })

export const toggleAtivoProduto = (id) => api.patch(`/produtos/${id}/toggle-ativo`)
