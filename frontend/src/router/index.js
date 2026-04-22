import { createRouter, createWebHistory } from 'vue-router'

// Lazy-loaded routes for better performance
const LoginView      = () => import('../views/LoginView.vue')
const DashboardView  = () => import('../views/DashboardView.vue')
const ProdutosView   = () => import('../views/ProdutosView.vue')
const ComprasView    = () => import('../views/ComprasView.vue')
const VendasView     = () => import('../views/VendasView.vue')
const RelatoriosView = () => import('../views/RelatoriosView.vue')
const NotFoundView   = () => import('../views/NotFoundView.vue')

const routes = [
  { path: '/login',      name: 'login',      component: LoginView,      meta: { guest: true } },
  { path: '/',           redirect: '/dashboard' },
  { path: '/dashboard',  name: 'dashboard',  component: DashboardView,  meta: { auth: true,  title: 'Dashboard' } },
  { path: '/produtos',   name: 'produtos',   component: ProdutosView,   meta: { auth: true,  title: 'Produtos' } },
  { path: '/compras',    name: 'compras',    component: ComprasView,    meta: { auth: true,  title: 'Compras' } },
  { path: '/vendas',     name: 'vendas',     component: VendasView,     meta: { auth: true,  title: 'Vendas' } },
  { path: '/relatorios', name: 'relatorios', component: RelatoriosView, meta: { auth: true,  title: 'Relatórios' } },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFoundView },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(_to, _from, savedPosition) {
    return savedPosition || { top: 0 }
  },
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.auth && !token) {
    return next({ name: 'login' })
  }

  if (to.meta.guest && token) {
    return next({ name: 'dashboard' })
  }

  // Update page title
  document.title = to.meta.title ? `${to.meta.title} — ERP Estoque` : 'ERP Estoque'

  next()
})

export default router
