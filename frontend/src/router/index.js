import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/HomeView.vue'),
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/auth/RegisterView.vue'),
    meta: { guest: true },
  },
  {
    path: '/posts',
    name: 'Posts',
    component: () => import('@/views/posts/PostListView.vue'),
  },
  {
    path: '/posts/create',
    name: 'CreatePost',
    component: () => import('@/views/posts/PostCreateView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/posts/:id',
    name: 'PostDetail',
    component: () => import('@/views/posts/PostDetailView.vue'),
  },
  {
    path: '/posts/:id/edit',
    name: 'EditPost',
    component: () => import('@/views/posts/PostEditView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/my-posts',
    name: 'MyPosts',
    component: () => import('@/views/posts/MyPostsView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/profile/:id',
    name: 'Profile',
    component: () => import('@/views/profile/ProfileView.vue'),
  },
  {
    path: '/news',
    name: 'News',
    component: () => import('@/views/news/NewsView.vue'),
  },
  {
    path: '/jobs',
    name: 'Jobs',
    component: () => import('@/views/jobs/JobsView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'Home' })
  } else {
    next()
  }
})

export default router
