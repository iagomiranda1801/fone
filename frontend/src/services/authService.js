import api from './api'

export const login = (credentials) => api.post('/login', credentials)

export const getMe = () => api.get('/me')

export const logout = () => api.post('/logout')
