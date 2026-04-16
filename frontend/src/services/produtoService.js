import api from './api'

export const getProdutos = () => api.get('/produtos')

export const criarProduto = (dados) => api.post('/produtos', dados)
