<template>
  <div class="min-h-screen flex">

    <!-- Left Panel — Branding (hidden on mobile) -->
    <div class="hidden lg:flex lg:w-5/12 xl:w-[45%] bg-slate-900 flex-col justify-between p-12 relative overflow-hidden select-none">
      <!-- Subtle grid overlay -->
      <div class="absolute inset-0 opacity-[0.04]" aria-hidden="true"
        style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 48px 48px;">
      </div>
      <!-- Accent blobs -->
      <div class="absolute -top-24 -right-24 w-72 h-72 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

      <!-- Logo -->
      <div class="relative z-10">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/30">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <span class="text-white font-semibold text-lg tracking-tight">ERP Estoque</span>
        </div>
      </div>

      <!-- Headline -->
      <div class="relative z-10">
        <h2 class="text-4xl font-bold text-white leading-snug mb-4">
          Gestão de estoque<br />simplificada.
        </h2>
        <p class="text-slate-400 text-base leading-relaxed max-w-xs">
          Controle compras, vendas e inventário com precisão e eficiência em um único sistema.
        </p>

        <!-- Feature bullets -->
        <ul class="mt-8 space-y-3">
          <li v-for="feat in features" :key="feat" class="flex items-center gap-3 text-slate-300 text-sm">
            <div class="w-5 h-5 rounded-full bg-blue-500/20 border border-blue-500/40 flex items-center justify-center shrink-0">
              <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            {{ feat }}
          </li>
        </ul>
      </div>

      <!-- Footer -->
      <p class="relative z-10 text-slate-600 text-xs">© 2026 ERP Estoque. Todos os direitos reservados.</p>
    </div>

    <!-- Right Panel — Login Form -->
    <div class="flex-1 flex items-center justify-center bg-gray-50 p-6 sm:p-10">
      <div class="w-full max-w-[380px] login-form-enter">

        <!-- Mobile logo -->
        <div class="lg:hidden mb-10 flex items-center gap-3">
          <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <span class="text-gray-900 font-semibold text-lg">ERP Estoque</span>
        </div>

        <!-- Form header -->
        <div class="mb-8">
          <h1 class="text-2xl font-bold text-gray-900">Bem-vindo de volta</h1>
          <p class="text-gray-500 text-sm mt-1">Acesse o sistema de gestão de estoque</p>
        </div>

        <!-- Error Alert -->
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 -translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="error" class="mb-5 p-3.5 rounded-lg bg-red-50 border border-red-200 flex items-start gap-2.5">
            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-red-700">{{ error }}</span>
          </div>
        </Transition>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-5">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="seu@email.com"
                required
                autocomplete="email"
                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 bg-white
                       text-gray-900 placeholder:text-gray-400 text-sm
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                       outline-none transition-all duration-150"
              />
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                required
                autocomplete="current-password"
                class="w-full pl-10 pr-11 py-2.5 rounded-lg border border-gray-300 bg-white
                       text-gray-900 placeholder:text-gray-400 text-sm
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20
                       outline-none transition-all duration-150"
              />
              <button
                type="button"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
              >
                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-2.5 rounded-lg font-semibold text-sm text-white
                   bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                   disabled:opacity-60 disabled:cursor-not-allowed
                   transition-colors duration-150 flex items-center justify-center gap-2"
          >
            <svg
              v-if="loading"
              class="animate-spin h-4 w-4"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>

        <!-- Credentials hint -->
        <div class="mt-8 p-4 bg-white rounded-lg border border-gray-200">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Acesso de demonstração</p>
          <div class="space-y-0.5 text-sm text-gray-600 font-mono">
            <p>admin@erp.com</p>
            <p>admin123</p>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { login } = useAuth()

const form = reactive({ email: '', password: '' })
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const features = [
  'Controle de estoque em tempo real',
  'Gestão de compras e fornecedores',
  'Relatórios de vendas e lucro',
]

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await login({ email: form.email, password: form.password })
    router.push('/dashboard')
  } catch (err) {
    error.value = typeof err.message === 'string' ? err.message : 'Erro ao fazer login. Tente novamente.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-form-enter {
  animation: formEnter 0.4s ease-out both;
}

@keyframes formEnter {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
