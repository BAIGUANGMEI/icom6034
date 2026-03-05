<script setup>
import { onMounted } from 'vue'
import AppHeader from './components/layout/AppHeader.vue'
import AppFooter from './components/layout/AppFooter.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  if (authStore.isAuthenticated) {
    authStore.fetchUser()
  }
})
</script>

<template>
  <div id="app">
    <AppHeader />
    <main class="app-main">
      <div class="container">
        <router-view />
      </div>
    </main>
    <AppFooter />
  </div>
</template>

<style scoped>
.app-main {
  flex: 1;
  padding: var(--space-xl) 0;
}
</style>
