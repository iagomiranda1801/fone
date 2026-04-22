import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
      return Promise.reject({ message: 'Sessão expirada. Faça login novamente.', status: 401 })
    }

    // Laravel validation errors (422) come as { errors: { field: ['msg'] } }
    if (error.response?.status === 422) {
      const rawErrors = error.response.data?.errors
      const message = rawErrors
        ? Object.values(rawErrors).flat().join('\n')
        : error.response.data?.message || 'Dados inválidos. Verifique os campos.'
      return Promise.reject({ message, status: 422 })
    }

    // Network error (no response)
    if (!error.response) {
      return Promise.reject({ message: 'Sem conexão com o servidor. Verifique sua rede.', status: 0 })
    }

    const message =
      error.response?.data?.message ||
      'Erro inesperado. Tente novamente.'

    return Promise.reject({ message, status: error.response?.status })
  },
)

export default api
