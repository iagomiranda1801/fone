<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 px-4">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-xl shadow-indigo-500/25 mb-6">
        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h1 class="text-7xl font-black text-gray-900 tracking-tighter mb-2">404</h1>
      <p class="text-xl font-bold text-gray-600 mb-1">Página não encontrada</p>
      <p class="text-sm text-gray-400 mb-8">O endereço que você acessou não existe.</p>
      <RouterLink
        to="/dashboard"
        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600
          text-white font-semibold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:from-indigo-600
          hover:to-purple-700 transition-all duration-200"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar ao Dashboard
      </RouterLink>
    </div>
  </div>
</template>
