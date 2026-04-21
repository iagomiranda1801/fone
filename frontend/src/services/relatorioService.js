import api from './api'

export const getRelatorioVendas    = (params) => api.get('/relatorios/vendas', { params })
export const getRelatorioCompras   = (params) => api.get('/relatorios/compras', { params })
export const getRelatorioEstoque   = (params) => api.get('/relatorios/estoque', { params })
export const getRelatorioLucratividade = (params) => api.get('/relatorios/lucratividade', { params })
export const getProdutosLista      = ()        => api.get('/relatorios/produtos-lista')
