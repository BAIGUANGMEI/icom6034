import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function register(data) {
    // TODO: Implement register action
  }

  async function login(data) {
    // TODO: Implement login action
  }

  async function logout() {
    // TODO: Implement logout action
  }

  async function fetchUser() {
    // TODO: Implement fetch user action
  }

  return {
    user,
    token,
    isAuthenticated,
    register,
    login,
    logout,
    fetchUser,
  }
})
