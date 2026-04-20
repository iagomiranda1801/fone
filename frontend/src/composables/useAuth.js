import { ref, computed } from 'vue'
import { login as loginApi, logout as logoutApi, getMe } from '../services/authService'

const user = ref(JSON.parse(localStorage.getItem('user')))
const token = ref(localStorage.getItem('token'))

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials) {
    const { data } = await loginApi(credentials)
    token.value = data.token
    user.value = data.user
    localStorage.setItem('token', data.token)
    localStorage.setItem('user', JSON.stringify(data.user))
    return data
  }

  async function fetchUser() {
    try {
      const { data } = await getMe()
      user.value = data
      localStorage.setItem('user', JSON.stringify(data))
      return data
    } catch {
      clearAuth()
      throw new Error('Sessão expirada.')
    }
  }

  async function logout() {
    try {
      await logoutApi()
    } finally {
      clearAuth()
    }
  }

  function clearAuth() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  function getToken() {
    return token.value
  }

  return { user, token, isAuthenticated, login, logout, fetchUser, getToken, clearAuth }
}
