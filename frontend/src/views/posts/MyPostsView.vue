<template>
  <div class="my-posts-view">
    <div class="my-posts-toolbar">
      <div>
        <h1 class="page-title">My Posts</h1>
        <p class="page-desc">Review, edit, or delete the posts you have shared.</p>
      </div>
      <router-link :to="{ name: 'CreatePost' }" class="btn btn-primary">
        Create Post
      </router-link>
    </div>
    <div v-if="loading" class="loading card">Loading…</div>
    <template v-else>
      <div v-if="myPosts.length" class="post-list">
        <article
          v-for="post in myPosts"
          :key="post.id"
          class="card post-card post-card--clickable"
          role="button"
          tabindex="0"
          @click="goToDetail(post.id)"
          @keydown.enter="goToDetail(post.id)"
          @keydown.space.prevent="goToDetail(post.id)"
        >
          <h2 class="post-card-title">
            <router-link :to="{ name: 'PostDetail', params: { id: post.id } }" @click.stop>
              {{ post.title }}
            </router-link>
          </h2>
          <p class="post-card-excerpt">{{ excerpt(post.content) }}</p>
          <div class="post-card-meta">
            <span class="date">{{ formatDate(post.created_at) }}</span>
            <span v-if="post.comments_count != null" class="comments-count">
              {{ post.comments_count }} comment(s)
            </span>
          </div>
          <div v-if="post.tags?.length" class="post-card-tags">
            <span v-for="tag in post.tags" :key="tag.id" class="badge">{{ tag.name }}</span>
          </div>
          <div class="post-card-actions" @click.stop>
            <router-link :to="{ name: 'EditPost', params: { id: post.id } }" class="btn btn-outline btn-sm">
              Edit
            </router-link>
            <button
              type="button"
              class="btn btn-ghost btn-sm post-card-delete"
              @click="openDeleteModal(post)"
            >
              Delete
            </button>
          </div>
        </article>
      </div>
      <div v-else class="empty-state card">
        <p class="empty-state-title">You haven’t posted yet</p>
        <p class="empty-state-hint">Share your first post with the community.</p>
        <router-link :to="{ name: 'CreatePost' }" class="btn btn-primary">Create Post</router-link>
      </div>
      <ConfirmModal
        v-model:open="showDeleteModal"
        title="Delete post"
        :message="deleteModalMessage"
        confirm-text="Delete"
        cancel-text="Cancel"
        variant="danger"
        :loading="deleting"
        loading-text="Deleting…"
        @confirm="handleDeleteConfirm"
      />
      <div v-if="myPosts.length && pagination.meta?.last_page > 1" class="pagination">
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
 * My Posts page: list of current user's posts with Edit/Delete. Create Post CTA. Pagination.
 */
import { onMounted, computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostStore } from '@/stores/post'
import ConfirmModal from '@/components/ConfirmModal.vue'

const router = useRouter()
const postStore = usePostStore()
const { myPosts, loading, pagination } = storeToRefs(postStore)

const currentPage = computed(() => pagination.value.meta?.current_page ?? 1)
const showDeleteModal = ref(false)
const postToDelete = ref(null)
const deleting = ref(false)

const deleteModalMessage = computed(() =>
  postToDelete.value
    ? `Delete "${postToDelete.value.title}"? This cannot be undone.`
    : ''
)

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

function goToDetail(id) {
  router.push({ name: 'PostDetail', params: { id } })
}

function goPage(page) {
  postStore.fetchMyPosts({ page })
}

function openDeleteModal(post) {
  postToDelete.value = post
  showDeleteModal.value = true
}

async function handleDeleteConfirm() {
  if (!postToDelete.value) return
  deleting.value = true
  try {
    await postStore.deletePost(postToDelete.value.id)
    showDeleteModal.value = false
    postToDelete.value = null
    await postStore.fetchMyPosts({ page: currentPage.value })
  } catch (e) {
    console.error(e)
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  postStore.fetchMyPosts()
})
</script>

<style scoped>
.my-posts-view {
  display: flex;
  flex-direction: column;
  gap: var(--space-xl);
}

.my-posts-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-md);
}

.page-title {
  margin: 0 0 var(--space-sm);
  font-size: clamp(1.8rem, 2.8vw, 2.4rem);
}

.page-desc {
  max-width: 48ch;
  color: var(--color-text-secondary);
}

.post-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
}

.post-card {
  padding: var(--space-lg);
  box-shadow: var(--shadow-soft);
  transition:
    transform var(--transition-normal),
    box-shadow var(--transition-normal);
}

.post-card--clickable {
  cursor: pointer;
}

.post-card:hover {
  transform: rotate(-1deg) translateY(-3px);
  box-shadow: var(--shadow-pink);
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

.post-card-tags {
  margin-bottom: var(--space-md);
}

.post-card-tags .badge {
  margin-right: var(--space-sm);
}

.post-card-actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
  padding-top: var(--space-md);
  border-top: var(--border-width) dashed var(--color-border-soft);
}

.post-card-delete {
  color: var(--color-danger);
}

.post-card-delete:hover {
  color: var(--color-danger);
}

.loading {
  padding: var(--space-2xl);
  text-align: center;
  color: var(--color-text-secondary);
}

.empty-state {
  padding: var(--space-2xl);
  text-align: center;
}

.empty-state-title {
  margin: 0 0 var(--space-xs);
  color: var(--color-text-secondary);
  font-size: 1.125rem;
}

.empty-state-hint {
  margin: 0 0 var(--space-lg);
  color: var(--color-text-muted);
  font-size: 0.9375rem;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: var(--space-md);
  margin-top: var(--space-xl);
}

.page-info {
  color: var(--color-text-secondary);
  font-size: 0.875rem;
}

@media (max-width: 640px) {
  .my-posts-toolbar .btn {
    width: 100%;
  }

  .post-card-actions .btn {
    width: 100%;
  }
}
</style>
