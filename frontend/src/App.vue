<template>
  <AppLayout v-if="$route.meta.auth">
    <RouterView />
  </AppLayout>
  <RouterView v-else />
</template>

<script setup>
import { RouterView } from 'vue-router'
import AppLayout from './components/layout/AppLayout.vue'
</script>
