<template>
  <div class="post-list-view container">
    <h1 class="page-title">All Posts</h1>
    <div class="list-actions">
      <router-link v-if="authStore.isAuthenticated" :to="{ name: 'CreatePost' }" class="btn btn-primary">
        Create Post
      </router-link>
    </div>
    <div v-if="loading" class="loading">Loading…</div>
    <template v-else>
      <div v-if="posts.length" class="post-list">
        <article
          v-for="post in posts"
          :key="post.id"
          class="card post-card post-card--clickable"
          role="button"
          tabindex="0"
          @click="goToPost(post.id)"
          @keydown.enter="goToPost(post.id)"
          @keydown.space.prevent="goToPost(post.id)"
        >
          <h2 class="post-card-title">
            <router-link :to="{ name: 'PostDetail', params: { id: post.id } }" class="post-card-title-link" @click.stop>
              {{ post.title }}
            </router-link>
          </h2>
          <p class="post-card-excerpt">{{ excerpt(post.content) }}</p>
          <div class="post-card-meta">
            <router-link
              v-if="post.user"
              :to="{ name: 'Profile', params: { id: post.user.id } }"
              class="author"
              @click.stop
            >
              {{ post.user.name }}
            </router-link>
            <span class="date">{{ formatDate(post.created_at) }}</span>
            <span v-if="post.comments_count != null" class="comments-count">
              {{ post.comments_count }} comment(s)
            </span>
          </div>
          <div v-if="post.tags?.length" class="post-card-tags">
            <span v-for="tag in post.tags" :key="tag.id" class="badge">{{ tag.name }}</span>
          </div>
        </article>
      </div>
      <p v-else class="text-muted empty">No posts yet. Create the first one!</p>
      <div v-if="pagination.meta?.last_page > 1" class="pagination">
        <button
          class="btn btn-outline btn-sm"
          :disabled="!pagination.links?.prev"
          @click="goPage(currentPage - 1)"
        >
          Previous
        </button>
        <span class="page-info">
          {{ currentPage }} / {{ pagination.meta.last_page }}
        </span>
        <button
          class="btn btn-outline btn-sm"
          :disabled="!pagination.links?.next"
          @click="goPage(currentPage + 1)"
        >
          Next
        </button>
      </div>
    </template>
  </div>
</template>

<script setup>
/**
 * Post list page: paginated list of posts with title, plain-text excerpt, author, date, comment count, tags.
 * Create Post button shown when authenticated. Previous/Next for pagination.
 */
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostStore } from '@/stores/post'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const postStore = usePostStore()
const authStore = useAuthStore()
const { posts, loading, pagination } = storeToRefs(postStore)

const currentPage = computed(() => pagination.value.meta?.current_page ?? 1)

/** Strip HTML and take first 150 chars for list excerpt */
function excerpt(html) {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  const text = div.textContent || div.innerText || ''
  return text.slice(0, 150) + (text.length > 150 ? '…' : '')
}

function formatDate(val) {
  if (!val) return ''
  return new Date(val).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

function goToPost(id) {
  router.push({ name: 'PostDetail', params: { id } })
}

function goPage(page) {
  postStore.fetchPosts({ page })
}

onMounted(() => {
  postStore.fetchPosts()
})
</script>

<style scoped>
.post-list-view {
  padding: var(--space-lg) var(--space-md);
}

.page-title {
  margin-bottom: var(--space-lg);
  color: var(--color-heading);
  font-size: 1.5rem;
}

.list-actions {
  margin-bottom: var(--space-lg);
}

.post-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
}

.post-card {
  padding: var(--space-lg);
  transition: box-shadow var(--transition-fast);
}

.post-card--clickable {
  cursor: pointer;
}

.post-card:hover {
  box-shadow: var(--shadow-md);
}

.post-card-title {
  margin-bottom: var(--space-sm);
  font-size: 1.125rem;
}

.post-card-title a {
  color: var(--color-heading);
  text-decoration: none;
}

.post-card-title a:hover {
  color: var(--color-primary);
}

.post-card-excerpt {
  margin-bottom: var(--space-md);
  color: var(--color-text-secondary);
  font-size: 0.9375rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.post-card-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-md);
  margin-bottom: var(--space-sm);
  color: var(--color-text-muted);
  font-size: 0.875rem;
}

.post-card-meta .author {
  color: var(--color-primary);
  text-decoration: none;
}

.post-card-tags .badge {
  margin-right: var(--space-sm);
}

.loading,
.empty {
  padding: var(--space-xl);
  text-align: center;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-md);
  margin-top: var(--space-xl);
}

.page-info {
  color: var(--color-text-secondary);
  font-size: 0.875rem;
}
</style>
