import api from './api'

export const getCompras = () => api.get('/compras')

export const registrarCompra = (dados) => api.post('/compras', dados)
