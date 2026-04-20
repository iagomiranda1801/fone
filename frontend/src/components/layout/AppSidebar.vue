<template>
  <aside
    class="w-64 bg-white border-r border-gray-200/60 flex flex-col h-screen"
    :class="mobile ? '' : 'hidden lg:flex fixed inset-y-0 left-0 z-30'"
  >
    <!-- Brand -->
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/20">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
        </div>
        <div>
          <h1 class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent leading-tight">
            ERP Estoque
          </h1>
          <p class="text-[11px] text-gray-400 font-medium">Gestão de Estoque</p>
        </div>
        <button
          v-if="mobile"
          @click="$emit('close')"
          class="ml-auto p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <p class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menu</p>
      <RouterLink
        v-for="link in links"
        :key="link.to"
        :to="link.to"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group"
        :class="
          $route.path === link.to
            ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-700 shadow-sm border border-indigo-100/50'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
        "
        @click="mobile && $emit('close')"
      >
        <div
          class="flex items-center justify-center w-9 h-9 rounded-lg transition-all duration-200"
          :class="
            $route.path === link.to
              ? 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-500/25'
              : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200 group-hover:text-gray-700'
          "
        >
          <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="link.icon"></svg>
        </div>
        <div>
          <span class="block leading-tight">{{ link.label }}</span>
          <span class="text-[11px] font-normal" :class="$route.path === link.to ? 'text-indigo-500/70' : 'text-gray-400'">{{ link.description }}</span>
        </div>
      </RouterLink>
    </nav>

    <!-- User Section -->
    <div class="border-t border-gray-100 px-4 py-4">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-md shadow-indigo-500/20">
          {{ userInitials }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-gray-800 truncate">{{ user?.name }}</p>
          <p class="text-[11px] text-gray-400 truncate">{{ user?.email }}</p>
        </div>
        <button
          @click="handleLogout"
          class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all duration-200"
          title="Sair do sistema"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuth } from '../../composables/useAuth'

defineProps({
  mobile: { type: Boolean, default: false },
})
defineEmits(['close'])

const router = useRouter()
const { user, logout } = useAuth()

const userInitials = computed(() => {
  if (!user.value?.name) return '?'
  return user.value.name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase()
})

const links = [
  {
    to: '/produtos',
    label: 'Produtos',
    description: 'Cadastro e estoque',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
  },
  {
    to: '/compras',
    label: 'Compras',
    description: 'Entrada de mercadorias',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />',
  },
  {
    to: '/vendas',
    label: 'Vendas',
    description: 'Saída de mercadorias',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" />',
  },
]

async function handleLogout() {
  await logout()
  router.push('/login')
}
</script>
