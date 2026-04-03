<template>
  <header class="header">
    <div class="header-inner container">
      <router-link to="/" class="logo">
        <svg class="logo-icon" viewBox="0 0 44 44" width="44" height="44" fill="none">
          <rect x="1" y="1" width="42" height="42" rx="16" fill="#8B5CF6" />
          <circle cx="33" cy="11" r="5" fill="#FBBF24" />
          <text x="22" y="28" text-anchor="middle" font-size="18" font-weight="800" fill="#fff">J</text>
        </svg>
        <span class="logo-copy">
          <span class="logo-text">JobShare</span>
          <span class="logo-tag">Stories, jobs, and confetti energy</span>
        </span>
      </router-link>

      <nav class="nav-links">
        <router-link to="/" class="nav-item" exact-active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M4 21V9l8-6 8 6v12h-6v-6h-4v6H4Z"/></svg>
          <span>Home</span>
        </router-link>
        <router-link to="/posts" class="nav-item" active-class="active">
          <svg class="nav-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm0 16H5V5h14v14ZM7 7h10v2H7V7Zm0 4h10v2H7v-2Zm0 4h7v2H7v-2Z"/></svg>
          <span>Posts</span>
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

      <div class="header-right">
        <template v-if="authStore.isAuthenticated">
          <router-link to="/posts/create" class="btn btn-sm btn-outline post-btn">
            <svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>
            Post
          </router-link>

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

    <Transition name="slide">
      <nav class="mobile-nav" v-show="mobileOpen">
        <router-link to="/" class="mobile-link" @click="mobileOpen = false">Home</router-link>
        <router-link to="/posts" class="mobile-link" @click="mobileOpen = false">Posts</router-link>
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
.header {
  position: sticky;
  top: 0;
  z-index: 100;
  padding: var(--space-md) 0;
  backdrop-filter: blur(10px);
}

.header-inner {
  display: flex;
  align-items: center;
  min-height: var(--header-height);
  gap: var(--space-lg);
  padding-top: var(--space-sm);
  padding-bottom: var(--space-sm);
  border: none;
  border-radius: var(--radius-xl);
  background: rgba(255, 255, 255, 0.88);
  box-shadow: var(--shadow-soft);
  position: relative;
}

.logo {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  flex-shrink: 0;
  padding-left: var(--space-lg);
}

.logo-icon {
  flex-shrink: 0;
}

.logo-copy {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.logo-text {
  font-family: 'Outfit', system-ui, sans-serif;
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--color-heading);
  letter-spacing: -0.04em;
}

.logo-tag {
  color: var(--color-text-secondary);
  font-size: 0.73rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.07em;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-left: var(--space-lg);
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 8px;
  min-height: 48px;
  padding: 0 18px;
  border: var(--border-width) solid transparent;
  border-radius: var(--radius-full);
  color: var(--color-text-secondary);
  font-family: 'Outfit', system-ui, sans-serif;
  font-size: 0.9rem;
  font-weight: 700;
  position: relative;
  white-space: nowrap;
}

.nav-item:hover {
  transform: translateY(-2px);
  color: var(--color-heading);
  background: var(--color-primary-lighter);
}

.nav-item.active {
  color: var(--color-heading);
  background: var(--color-tertiary);
  box-shadow: var(--shadow-pop);
}

.nav-icon {
  width: 20px;
  height: 20px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  margin-left: auto;
  padding-right: var(--space-lg);
}

.post-btn svg {
  flex-shrink: 0;
}

.avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: var(--color-quaternary);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-family: 'Outfit', system-ui, sans-serif;
  font-size: 0.95rem;
  flex-shrink: 0;
  box-shadow: 3px 3px 0 0 var(--color-foreground);
}

.avatar-lg {
  width: 52px;
  height: 52px;
  font-size: 1.3rem;
}

.user-menu {
  position: relative;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  padding: 6px 10px;
  border: none;
  border-radius: var(--radius-full);
  background: #fff;
}

.user-menu:hover {
  background: var(--color-primary-lighter);
}

.caret {
  color: var(--color-text-secondary);
}

.dropdown {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  width: 300px;
  background: var(--color-surface);
  border: none;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-soft);
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
  font-family: 'Outfit', system-ui, sans-serif;
  font-weight: 700;
  color: var(--color-heading);
  font-size: 1rem;
}

.dropdown-email {
  font-size: 0.82rem;
  color: var(--color-text-secondary);
}

.dropdown-divider {
  height: var(--border-width);
  background: var(--color-border-soft);
  margin: var(--space-xs) 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  font-size: 0.92rem;
  color: var(--color-foreground);
  background: none;
  border: none;
  text-align: left;
}

.dropdown-item:hover {
  background: var(--color-tertiary);
}

.dropdown-item svg {
  color: var(--color-text-secondary);
  flex-shrink: 0;
}

.mobile-toggle {
  display: none;
  width: 46px;
  height: 46px;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 50%;
  background: var(--color-secondary);
  color: #fff;
  box-shadow: var(--shadow-pop);
}

.mobile-nav {
  display: none;
  flex-direction: column;
  gap: var(--space-sm);
  margin: var(--space-sm) auto 0;
  width: min(100%, calc(var(--content-max-width) - 32px));
  background: rgba(255, 255, 255, 0.96);
  border: none;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-soft);
  padding: var(--space-md);
}

.mobile-link {
  display: block;
  padding: 12px 16px;
  color: var(--color-foreground);
  font-family: 'Outfit', system-ui, sans-serif;
  font-size: 0.96rem;
  font-weight: 700;
  border: var(--border-width) solid transparent;
  border-radius: var(--radius-full);
  background: none;
  text-align: left;
  width: 100%;
}

.mobile-link:hover,
.mobile-link.router-link-active {
  background: var(--color-tertiary);
  color: var(--color-heading);
}

.logout-link {
  color: var(--color-danger);
}

.slide-enter-active,
.slide-leave-active {
  transition: all 0.22s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-12px) scale(0.98);
}

@media (max-width: 1080px) {
  .logo-tag {
    display: none;
  }

  .nav-links {
    gap: 4px;
  }

  .nav-item {
    padding: 0 14px;
  }
}

@media (max-width: 860px) {
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

  .header-inner {
    padding-left: 0;
    padding-right: 0;
  }
  .logo {
    padding-left: var(--space-md);
  }

  .header-right {
    padding-right: var(--space-md);
  }
}

@media (max-width: 640px) {
  .header {
    padding-top: var(--space-sm);
  }

  .logo-text {
    font-size: 1.05rem;
  }
}
</style>
