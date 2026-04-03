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
      <div class="app-main-inner container">
        <router-view />
      </div>
    </main>
    <AppFooter />
  </div>
</template>

<style scoped>
.app-main {
  flex: 1;
  position: relative;
  padding: var(--space-3xl) 0 var(--space-4xl);
}

.app-main-inner {
  position: relative;
  z-index: 1;
}

@media (max-width: 768px) {
  .app-main {
    padding: var(--space-2xl) 0 var(--space-3xl);
  }
}
</style>
