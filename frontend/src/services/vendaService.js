import api from './api'

export const getVendas = () => api.get('/vendas')

export const registrarVenda = (dados) => api.post('/vendas', dados)

export const cancelarVenda = (id) => api.patch(`/vendas/${id}/cancelar`)
