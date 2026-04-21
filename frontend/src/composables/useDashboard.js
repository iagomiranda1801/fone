import { ref } from 'vue'
import { getDashboard } from '../services/dashboardService'

export function useDashboard() {
  const data = ref(null)
  const loading = ref(false)

  async function carregar() {
    loading.value = true
    try {
      const response = await getDashboard()
      data.value = response.data
    } finally {
      loading.value = false
    }
  }

  return { data, loading, carregar }
}
