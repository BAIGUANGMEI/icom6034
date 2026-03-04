<template>
  <header class="app-header">
    <nav>
      <router-link to="/">Home</router-link>
      <router-link to="/posts">Posts</router-link>
      <router-link to="/search">Search</router-link>
      <router-link to="/news">News</router-link>

      <template v-if="authStore.isAuthenticated">
        <router-link to="/posts/create">New Post</router-link>
        <router-link to="/my-posts">My Posts</router-link>
        <span>Welcome, {{ authStore.user?.name }}</span>
        <button @click="handleLogout">Logout</button>
      </template>
      <template v-else>
        <router-link to="/login">Login</router-link>
        <router-link to="/register">Register</router-link>
      </template>
    </nav>
  </header>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

async function handleLogout() {
  // TODO: Implement logout
}
</script>

<style scoped>
/* TODO: Add header styles */
</style>
