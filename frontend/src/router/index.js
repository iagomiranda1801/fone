import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProdutosView from '../views/ProdutosView.vue'
import ComprasView from '../views/ComprasView.vue'
import VendasView from '../views/VendasView.vue'

const routes = [
  { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
  { path: '/', redirect: '/dashboard' },
  { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { auth: true } },
  { path: '/produtos', name: 'produtos', component: ProdutosView, meta: { auth: true } },
  { path: '/compras', name: 'compras', component: ComprasView, meta: { auth: true } },
  { path: '/vendas', name: 'vendas', component: VendasView, meta: { auth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.auth && !token) {
    return next({ name: 'login' })
  }

  if (to.meta.guest && token) {
    return next({ name: 'dashboard' })
  }

  next()
})

export default router
