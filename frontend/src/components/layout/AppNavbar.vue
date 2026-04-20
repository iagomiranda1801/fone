<template>
  <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200/60 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center gap-2">
          <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <span class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            ERP Estoque
          </span>
        </div>

        <!-- Navigation Links (desktop) -->
        <div class="hidden sm:flex items-center gap-1">
          <RouterLink
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
            :class="
              $route.path === link.to
                ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-700 shadow-sm'
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            <span class="flex items-center gap-2">
              <component :is="link.icon" />
              {{ link.label }}
            </span>
          </RouterLink>
        </div>

        <!-- User Menu -->
        <div class="flex items-center gap-3">
          <div class="hidden sm:flex items-center gap-2 text-sm text-gray-600">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs shadow-sm">
              {{ userInitials }}
            </div>
            <span class="font-medium text-gray-700">{{ user?.name }}</span>
          </div>
          <button
            @click="handleLogout"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium
                   text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all duration-200"
            title="Sair"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="hidden sm:inline">Sair</span>
          </button>

          <!-- Mobile menu button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="sm:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:bg-gray-100"
          >
            <svg v-if="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile menu -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="mobileMenuOpen" class="sm:hidden pb-4 pt-2 space-y-1">
          <div class="flex items-center gap-2 px-3 py-2 mb-2 text-sm text-gray-600 border-b border-gray-100 pb-3">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs">
              {{ userInitials }}
            </div>
            <span class="font-medium text-gray-700">{{ user?.name }}</span>
          </div>
          <RouterLink
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="block px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
            :class="
              $route.path === link.to
                ? 'bg-indigo-50 text-indigo-700'
                : 'text-gray-600 hover:bg-gray-50'
            "
            @click="mobileMenuOpen = false"
          >
            {{ link.label }}
          </RouterLink>
        </div>
      </Transition>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuth } from '../../composables/useAuth'

const router = useRouter()
const { user, logout } = useAuth()
const mobileMenuOpen = ref(false)

const userInitials = computed(() => {
  if (!user.value?.name) return '?'
  return user.value.name
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
})

const links = [
  { to: '/produtos', label: 'Produtos' },
  { to: '/compras', label: 'Compras' },
  { to: '/vendas', label: 'Vendas' },
]

async function handleLogout() {
  await logout()
  router.push('/login')
}
</script>
