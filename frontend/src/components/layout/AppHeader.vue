<template>
  <header class="header">
    <div class="header-inner container">
      <!-- Logo -->
      <router-link to="/" class="logo">
        <svg class="logo-icon" viewBox="0 0 32 32" width="28" height="28" fill="none">
          <rect width="32" height="32" rx="6" fill="#42b883" />
          <text x="16" y="22" text-anchor="middle" font-size="16" font-weight="700" fill="#fff">J</text>
        </svg>
        <span class="logo-text">JobShare</span>
      </router-link>

      <!-- Desktop Nav -->
      <nav class="nav-links">
        <router-link to="/" class="nav-item" exact-active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M4 21V9l8-6 8 6v12h-6v-6h-4v6H4Z"/></svg>
          <span>Home</span>
        </router-link>
        <router-link to="/posts" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm0 16H5V5h14v14ZM7 7h10v2H7V7Zm0 4h10v2H7v-2Zm0 4h7v2H7v-2Z"/></svg>
          <span>Posts</span>
        </router-link>
        <router-link to="/search" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5Zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14Z"/></svg>
          <span>Search</span>
        </router-link>
        <router-link to="/news" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 14H4V6h16v12ZM6 10h2v2H6v-2Zm0 4h2v2H6v-2Zm4-4h8v2h-8v-2Zm0 4h8v2h-8v-2Z"/></svg>
          <span>News</span>
        </router-link>
        <router-link to="/jobs" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2Zm-6 0h-4V4h4v2Z"/></svg>
          <span>Jobs</span>
        </router-link>
      </nav>

      <!-- Right Section -->
      <div class="header-right">
        <template v-if="authStore.isAuthenticated">
          <router-link to="/posts/create" class="btn btn-sm btn-outline post-btn">
            <svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>
            Post
          </router-link>

          <!-- User Menu -->
          <div class="user-menu" @click="toggleMenu" ref="menuRef">
            <div class="avatar">{{ userInitial }}</div>
            <svg class="caret" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="m7 10 5 5 5-5H7Z"/></svg>
            <div class="dropdown" v-show="menuOpen">
              <div class="dropdown-header">
                <div class="avatar avatar-lg">{{ userInitial }}</div>
                <div>
                  <div class="dropdown-name">{{ authStore.user?.name }}</div>
                  <div class="dropdown-email">{{ authStore.user?.email }}</div>
                </div>
              </div>
              <div class="dropdown-divider"></div>
              <router-link v-if="authStore.user" :to="{ name: 'Profile', params: { id: authStore.user.id } }" class="dropdown-item" @click.stop="menuOpen = false">
                <svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4Zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4Z"/></svg>
                My Profile
              </router-link>
              <router-link to="/my-posts" class="dropdown-item" @click.stop="menuOpen = false">
                <svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm4 18H6V4h7v5h5v11Z"/></svg>
                My Posts
              </router-link>
              <button class="dropdown-item" @click.stop="handleLogout">
                <svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5ZM4 5h8V3H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8v-2H4V5Z"/></svg>
                Log out
              </button>
            </div>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn-sm btn-ghost">Sign in</router-link>
          <router-link to="/register" class="btn btn-sm btn-primary">Join now</router-link>
        </template>

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" @click="mobileOpen = !mobileOpen" aria-label="Menu">
          <svg v-if="!mobileOpen" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M3 18h18v-2H3v2Zm0-5h18v-2H3v2Zm0-7v2h18V6H3Z"/></svg>
          <svg v-else viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41Z"/></svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <Transition name="slide">
      <nav class="mobile-nav" v-show="mobileOpen">
        <router-link to="/" class="mobile-link" @click="mobileOpen = false">Home</router-link>
        <router-link to="/posts" class="mobile-link" @click="mobileOpen = false">Posts</router-link>
        <router-link to="/search" class="mobile-link" @click="mobileOpen = false">Search</router-link>
        <router-link to="/news" class="mobile-link" @click="mobileOpen = false">News</router-link>
        <router-link to="/jobs" class="mobile-link" @click="mobileOpen = false">Jobs</router-link>
        <template v-if="authStore.isAuthenticated">
          <router-link to="/posts/create" class="mobile-link" @click="mobileOpen = false">New Post</router-link>
          <router-link v-if="authStore.user" :to="{ name: 'Profile', params: { id: authStore.user.id } }" class="mobile-link" @click="mobileOpen = false">My Profile</router-link>
          <router-link to="/my-posts" class="mobile-link" @click="mobileOpen = false">My Posts</router-link>
          <button class="mobile-link logout-link" @click="handleLogout">Log out</button>
        </template>
        <template v-else>
          <router-link to="/login" class="mobile-link" @click="mobileOpen = false">Sign in</router-link>
          <router-link to="/register" class="mobile-link" @click="mobileOpen = false">Join now</router-link>
        </template>
      </nav>
    </Transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const menuOpen = ref(false)
const mobileOpen = ref(false)
const menuRef = ref(null)

const userInitial = computed(() => {
  return authStore.user?.name?.charAt(0)?.toUpperCase() || 'U'
})

function toggleMenu() {
  menuOpen.value = !menuOpen.value
}

function handleClickOutside(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) {
    menuOpen.value = false
  }
}

async function handleLogout() {
  menuOpen.value = false
  mobileOpen.value = false
  await authStore.logout()
  router.push({ name: 'Login' })
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
/* ===== Header Bar ===== */
.header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
}

.header-inner {
  display: flex;
  align-items: center;
  height: var(--header-height);
  gap: var(--space-lg);
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  text-decoration: none;
  flex-shrink: 0;
}
.logo-icon {
  border-radius: 6px;
}
.logo-text {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-gray-900);
  letter-spacing: -0.3px;
}

/* Nav Links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 2px;
  margin-left: auto;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-sm);
  color: var(--color-gray-500);
  font-size: 12px;
  font-weight: 500;
  text-decoration: none;
  transition: color var(--transition-fast);
  position: relative;
}
.nav-item:hover {
  color: var(--color-gray-900);
}
.nav-item.active {
  color: var(--color-primary);
}
.nav-item.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  right: 0;
  height: 2px;
  background: var(--color-primary);
  border-radius: 2px 2px 0 0;
}
.nav-icon {
  width: 20px;
  height: 20px;
}

/* Right Section */
.header-right {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-left: auto;
}

.post-btn svg {
  flex-shrink: 0;
}

/* Avatar */
.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--color-primary);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  flex-shrink: 0;
}
.avatar-lg {
  width: 42px;
  height: 42px;
  font-size: 18px;
}

/* User Dropdown */
.user-menu {
  position: relative;
  display: flex;
  align-items: center;
  gap: 2px;
  cursor: pointer;
  padding: var(--space-xs);
  border-radius: var(--radius-sm);
}
.user-menu:hover {
  background: var(--color-gray-100);
}
.caret {
  color: var(--color-gray-500);
}

.dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 280px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  padding: var(--space-sm) 0;
  z-index: 200;
}

.dropdown-header {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  padding: var(--space-md);
}
.dropdown-name {
  font-weight: 600;
  color: var(--color-gray-900);
  font-size: 15px;
}
.dropdown-email {
  font-size: 13px;
  color: var(--color-gray-500);
}

.dropdown-divider {
  height: 1px;
  background: var(--color-border);
  margin: var(--space-xs) 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  font-size: 14px;
  color: var(--color-gray-700);
  text-decoration: none;
  transition: background var(--transition-fast);
  background: none;
  border: none;
  cursor: pointer;
  text-align: left;
}
.dropdown-item:hover {
  background: var(--color-gray-100);
}
.dropdown-item svg {
  color: var(--color-gray-500);
  flex-shrink: 0;
}

/* Mobile */
.mobile-toggle {
  display: none;
  padding: var(--space-xs);
  color: var(--color-gray-600);
}

.mobile-nav {
  display: none;
  flex-direction: column;
  background: var(--color-surface);
  border-top: 1px solid var(--color-border);
  padding: var(--space-sm);
}
.mobile-link {
  display: block;
  padding: var(--space-sm) var(--space-md);
  color: var(--color-gray-700);
  font-size: 15px;
  font-weight: 500;
  text-decoration: none;
  border-radius: var(--radius-sm);
  transition: background var(--transition-fast);
  background: none;
  border: none;
  text-align: left;
  width: 100%;
  cursor: pointer;
}
.mobile-link:hover,
.mobile-link.router-link-active {
  background: var(--color-gray-100);
  color: var(--color-primary);
}
.logout-link {
  color: var(--color-danger);
}

/* Transition */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.2s ease;
}
.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* Responsive */
@media (max-width: 768px) {
  .nav-links {
    display: none;
  }
  .post-btn {
    display: none;
  }
  .user-menu {
    display: none;
  }
  .mobile-toggle {
    display: flex;
  }
  .mobile-nav {
    display: flex;
  }
  .header-right .btn-ghost,
  .header-right .btn-primary {
    display: none;
  }
}
</style>
