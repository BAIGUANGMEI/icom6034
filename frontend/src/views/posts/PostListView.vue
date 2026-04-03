<template>
  <div class="post-list-view">
    <div class="list-actions">
      <div class="list-heading">
        <div>
          <h1 class="list-title">All Posts</h1>
          <p class="list-subtitle">Search by post title or tag, then browse the latest community posts.</p>
        </div>
        <div v-if="authStore.isAuthenticated" class="create-post-wrap">
          <router-link :to="{ name: 'CreatePost' }" class="btn btn-primary create-post-btn">
            Create Post
          </router-link>
        </div>
      </div>
      <form class="search-form" @submit.prevent="runSearch">
        <div class="search-field">
          <label for="post-title-search" class="search-label">Title</label>
          <input
            id="post-title-search"
            v-model="searchTitle"
            type="text"
            class="form-input search-input"
            placeholder="Search by title..."
            aria-label="Search posts by title"
          />
        </div>
        <div class="search-field search-field--tag">
          <label for="post-tag-search" class="search-label">Tag</label>
          <input
            id="post-tag-search"
            v-model="searchTag"
            type="text"
            class="form-input search-input search-input--tag"
            placeholder="frontend, interview..."
            aria-label="Filter by tag"
          />
        </div>
        <div class="search-actions">
          <button type="submit" class="btn btn-primary btn-sm">Search</button>
          <button v-if="isSearchActive" type="button" class="btn btn-outline btn-sm" @click="clearSearch">
            Clear
          </button>
        </div>
      </form>
    </div>
    <div v-if="loading" class="loading card">Loading…</div>
    <template v-else>
      <div v-if="posts.length" class="post-list">
        <article
          v-for="post in posts"
          :key="post.id"
          class="post-card post-card--clickable"
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
      <div v-else class="empty card">
        <p class="empty-title">{{ isSearchActive ? 'No posts match your search.' : 'No posts yet. Create the first one!' }}</p>
      </div>
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
 * Post list page: search (title + tag) and paginated list of posts.
 * Search is inline; no separate /search page.
 */
import { onMounted, computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostStore } from '@/stores/post'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const postStore = usePostStore()
const authStore = useAuthStore()
const { posts, loading, pagination } = storeToRefs(postStore)

const searchTitle = ref('')
const searchTag = ref('')

const currentPage = computed(() => pagination.value.meta?.current_page ?? 1)
const isSearchActive = computed(
  () => searchTitle.value.trim() !== '' || searchTag.value.trim() !== ''
)

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

function runSearch() {
  const title = searchTitle.value.trim()
  const tag = searchTag.value.trim()
  if (title || tag) {
    postStore.searchPosts({ title: title || undefined, tag: tag || undefined, page: 1 })
  } else {
    postStore.fetchPosts()
  }
}

function clearSearch() {
  searchTitle.value = ''
  searchTag.value = ''
  postStore.fetchPosts()
}

function goPage(page) {
  if (isSearchActive.value) {
    postStore.searchPosts({
      title: searchTitle.value.trim() || undefined,
      tag: searchTag.value.trim() || undefined,
      page,
    })
  } else {
    postStore.fetchPosts({ page })
  }
}

onMounted(() => {
  postStore.fetchPosts()
})
</script>

<style scoped>
.post-list-view {
  display: flex;
  flex-direction: column;
  gap: var(--space-xl);
}

.list-actions {
  display: grid;
  gap: var(--space-md);
}

.list-heading {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  justify-content: space-between;
  gap: var(--space-sm) var(--space-md);
}

.list-title {
  margin: 0;
  font-size: clamp(1.6rem, 2.2vw, 2rem);
}

.list-subtitle {
  margin: 0;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
}

.create-post-wrap {
  flex-shrink: 0;
}

.create-post-btn {
  min-width: 160px;
  justify-content: center;
}

.search-form {
  display: flex;
  flex-wrap: wrap;
  align-items: end;
  gap: var(--space-md);
  padding-top: var(--space-sm);
  border-top: 1px dashed var(--color-border);
}

.search-field {
  min-width: 220px;
  flex: 1;
}

.search-field--tag {
  flex: 0 0 220px;
}

.search-label {
  display: block;
  margin-bottom: var(--space-xs);
  font-family: 'Outfit', system-ui, sans-serif;
  font-size: 0.78rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.search-input {
  width: 100%;
}

.search-input--tag {
  width: 100%;
}

.search-actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
}

.post-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.post-card {
  padding: var(--space-lg) 0;
  border-bottom: 1px dashed var(--color-border);
  transition:
    transform var(--transition-normal),
    color var(--transition-normal);
}

.post-card--clickable {
  cursor: pointer;
}

.post-card:hover {
  transform: translateX(2px);
}

.post-card-title {
  margin-bottom: var(--space-sm);
  font-size: 1.125rem;
}

.post-card-title a {
  color: var(--color-heading);
}

.post-card-title a:hover {
  color: var(--color-secondary);
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
  color: var(--color-text-secondary);
  font-size: 0.875rem;
}

.post-card-meta .author {
  color: var(--color-primary-dark);
  font-weight: 700;
}

.post-card-tags .badge {
  margin-right: var(--space-sm);
  background: transparent;
  border-style: dashed;
  box-shadow: none;
  color: var(--color-text-secondary);
}

.loading,
.empty {
  padding: var(--space-2xl);
  text-align: center;
}

.empty-title {
  color: var(--color-text-secondary);
  font-size: 1rem;
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

@media (max-width: 640px) {
  .search-field,
  .search-field--tag,
  .search-actions,
  .search-actions .btn,
  .create-post-wrap,
  .create-post-wrap .btn {
    width: 100%;
  }

  .list-heading {
    align-items: flex-start;
    flex-direction: column;
  }

  .create-post-btn {
    min-width: 0;
  }
}
</style>
