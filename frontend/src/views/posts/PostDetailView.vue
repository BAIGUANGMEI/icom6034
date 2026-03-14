<template>
  <div class="post-detail-view container">
    <div v-if="loading" class="loading">Loading…</div>
    <template v-else-if="post">
      <article class="card post-detail">
        <h1 class="post-title">{{ post.title }}</h1>
        <div class="post-meta">
          <router-link
            v-if="post.user"
            :to="{ name: 'Profile', params: { id: post.user.id } }"
            class="author"
          >
            {{ post.user.name }}
          </router-link>
          <span class="date">{{ formatDate(post.created_at) }}</span>
          <span v-if="post.comments_count != null" class="comments-count">
            {{ post.comments_count }} comment(s)
          </span>
        </div>
        <div v-if="post.tags?.length" class="post-tags">
          <span v-for="tag in post.tags" :key="tag.id" class="badge">{{ tag.name }}</span>
        </div>
        <div class="post-body prose-content" v-html="post.content"></div>
        <div v-if="isAuthor" class="post-actions">
          <router-link :to="{ name: 'EditPost', params: { id: post.id } }" class="btn btn-outline btn-sm">
            Edit
          </router-link>
          <button type="button" class="btn btn-ghost btn-sm danger" @click="showDeleteModal = true">Delete</button>
        </div>
      </article>
      <section class="comments-section card">
        <div class="comments-section-header">
          <h2 class="comments-section-title">Comments</h2>
          <span v-if="post.comments_count != null" class="comments-count-badge">{{ post.comments_count }}</span>
        </div>
        <div v-if="authStore.isAuthenticated" class="comment-form">
          <textarea
            v-model="newComment"
            placeholder="Share your thoughts…"
            rows="3"
            class="comment-form-input"
          />
          <div class="comment-form-actions">
            <button
              type="button"
              class="btn btn-primary"
              :disabled="!newComment.trim() || submittingComment"
              @click="submitComment"
            >
              {{ submittingComment ? 'Submitting…' : 'Post comment' }}
            </button>
          </div>
        </div>
        <p v-else class="comment-login-hint">Log in to leave a comment.</p>
        <ul v-if="commentTree.length" class="comment-list">
          <CommentItem
            v-for="c in commentTree"
            :key="c.id"
            :comment="c"
            :replying-to-id="replyingToId"
            :reply-content="replyContent"
            :submitting-reply="submittingReply"
            :can-delete-comment="canDeleteComment"
            :format-date="formatDate"
            @reply-to="startReply"
            @submit-reply="submitReply"
            @cancel-reply="cancelReply"
            @update:replyContent="replyContent = $event"
            @delete="deleteComment"
          />
        </ul>
        <div v-else class="comments-empty">
          <p class="comments-empty-text">No comments yet.</p>
          <p class="comments-empty-hint">Be the first to share your thoughts.</p>
        </div>
      </section>
    </template>
    <p v-else class="text-muted">Post not found.</p>

    <ConfirmModal
      v-model:open="showDeleteModal"
      title="Delete post"
      message="Are you sure you want to delete this post? This cannot be undone."
      confirm-text="Delete"
      cancel-text="Cancel"
      variant="danger"
      :loading="deleting"
      loading-text="Deleting…"
      @confirm="handleDeleteConfirm"
    />
  </div>
</template>

<script setup>
/**
 * Post detail page: shows full post (title, author, date, tags, HTML content), edit/delete for author.
 * Comments: nested (reply to comment); add root comment or reply (if logged in), delete own. Content v-html.
 */
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostStore } from '@/stores/post'
import { useAuthStore } from '@/stores/auth'
import { commentApi } from '@/api/comments'
import CommentItem from '@/components/CommentItem.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'

const route = useRoute()
const router = useRouter()
const postStore = usePostStore()
const authStore = useAuthStore()
const { currentPost, loading } = storeToRefs(postStore)

const postId = computed(() => route.params.id)
const post = computed(() => currentPost.value)
const comments = ref([])
const newComment = ref('')
const submittingComment = ref(false)
const replyingToId = ref(null)
const replyContent = ref('')
const submittingReply = ref(false)
const showDeleteModal = ref(false)
const deleting = ref(false)

/** Build tree from flat list: root comments with recursive .replies */
const commentTree = computed(() => {
  const flat = comments.value
  if (!flat.length) return []
  const roots = flat.filter((c) => !c.parent_id).sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  const byParent = {}
  flat.forEach((c) => {
    if (c.parent_id) {
      if (!byParent[c.parent_id]) byParent[c.parent_id] = []
      byParent[c.parent_id].push(c)
    }
  })
  function addReplies(node) {
    const children = (byParent[node.id] || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
    return { ...node, replies: children.map(addReplies) }
  }
  return roots.map(addReplies)
})

const isAuthor = computed(() => {
  if (!post.value?.user || !authStore.user) return false
  return post.value.user.id === authStore.user.id
})

function formatDate(val) {
  if (!val) return ''
  const d = new Date(val)
  return d.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function loadPost() {
  try {
    await postStore.fetchPost(postId.value)
  } catch {
    router.replace({ name: 'Posts' })
  }
}

async function loadComments() {
  try {
    const { data } = await commentApi.getByPost(postId.value)
    comments.value = data.data ?? data ?? []
  } catch {
    comments.value = []
  }
}

watch(
  postId,
  () => {
    loadPost()
    loadComments()
  },
  { immediate: true }
)

async function handleDeleteConfirm() {
  deleting.value = true
  try {
    await postStore.deletePost(postId.value)
    showDeleteModal.value = false
    router.push({ name: 'Posts' })
  } catch (e) {
    console.error(e)
  } finally {
    deleting.value = false
  }
}

/** Post new root comment and prepend to list */
async function submitComment() {
  const content = newComment.value.trim()
  if (!content) return
  submittingComment.value = true
  try {
    const { data } = await commentApi.create(postId.value, { content })
    const created = data.data ?? data
    comments.value = [created, ...comments.value]
    newComment.value = ''
  } catch (e) {
    console.error(e)
  } finally {
    submittingComment.value = false
  }
}

function startReply(commentId) {
  replyingToId.value = commentId
  replyContent.value = ''
}

function cancelReply() {
  replyingToId.value = null
  replyContent.value = ''
}

/** Post reply to a comment (parent_id); append to flat list and clear reply form */
async function submitReply(parentId) {
  const content = replyContent.value.trim()
  if (!content) return
  submittingReply.value = true
  try {
    const { data } = await commentApi.create(postId.value, { content, parent_id: parentId })
    const created = data.data ?? data
    comments.value = [...comments.value, created]
    cancelReply()
  } catch (e) {
    console.error(e)
  } finally {
    submittingReply.value = false
  }
}

/** Only comment author can delete */
function canDeleteComment(c) {
  return authStore.user && c.user && c.user.id === authStore.user.id
}

async function deleteComment(id) {
  try {
    await commentApi.delete(postId.value, id)
    // Refetch so cascade-deleted replies are removed from the list
    await loadComments()
  } catch (e) {
    console.error(e)
  }
}
</script>

<style scoped>
.post-detail-view {
  padding: var(--space-lg) var(--space-md);
}

.loading {
  padding: var(--space-xl);
  text-align: center;
  color: var(--color-text-secondary);
}

.post-detail {
  padding: var(--space-xl);
  margin-bottom: var(--space-xl);
}

.post-title {
  margin-bottom: var(--space-sm);
  color: var(--color-heading);
  font-size: 1.75rem;
}

.post-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-md);
  margin-bottom: var(--space-md);
  color: var(--color-text-secondary);
  font-size: 0.875rem;
}

.post-meta .author {
  color: var(--color-primary);
  text-decoration: none;
}

.post-meta .author:hover {
  text-decoration: underline;
}

.post-tags {
  margin-bottom: var(--space-lg);
}

.post-tags .badge {
  margin-right: var(--space-sm);
}

.post-body {
  line-height: 1.7;
  color: var(--color-text-primary);
}

/* Safe prose styling for rich text content */
.prose-content :deep(p) {
  margin-bottom: var(--space-md);
}

.prose-content :deep(ul),
.prose-content :deep(ol) {
  margin-bottom: var(--space-md);
  padding-left: var(--space-lg);
}

.prose-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: var(--radius-sm);
  margin: var(--space-md) 0;
}

.prose-content :deep(h2) {
  margin: var(--space-lg) 0 var(--space-md);
  font-size: 1.25rem;
}

.post-actions {
  margin-top: var(--space-xl);
  padding-top: var(--space-lg);
  border-top: 1px solid var(--color-border);
  display: flex;
  gap: var(--space-md);
}

.post-actions .danger {
  color: var(--color-danger);
}

.comments-section {
  padding: var(--space-xl);
  border-radius: var(--radius-lg);
}

.comments-section-header {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-bottom: var(--space-lg);
  padding-bottom: var(--space-md);
  border-bottom: 1px solid var(--color-border);
}

.comments-section-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--color-heading);
}

.comments-count-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 1.5rem;
  height: 1.5rem;
  padding: 0 var(--space-xs);
  background: var(--color-primary-lighter);
  color: var(--color-primary-dark);
  border-radius: var(--radius-full);
  font-size: 0.8125rem;
  font-weight: 600;
}

.comment-form {
  margin-bottom: var(--space-xl);
  padding: var(--space-lg);
  background: var(--color-gray-50);
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
}

.comment-form-input {
  width: 100%;
  padding: var(--space-md);
  margin-bottom: var(--space-md);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-size: 0.9375rem;
  line-height: 1.5;
  resize: vertical;
  min-height: 80px;
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
}

.comment-form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-primary-lighter);
}

.comment-form-input::placeholder {
  color: var(--color-text-muted);
}

.comment-form-actions {
  display: flex;
  justify-content: flex-end;
}

.comment-login-hint {
  margin-bottom: var(--space-xl);
  padding: var(--space-md);
  text-align: center;
  color: var(--color-text-muted);
  font-size: 0.9375rem;
  background: var(--color-gray-50);
  border-radius: var(--radius-md);
}

.comment-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.comment-list > :deep(.comment-item) {
  padding: 0;
  border-bottom: 1px solid var(--color-border);
}

.comment-list > :deep(.comment-item:last-child) {
  border-bottom: none;
}

.comments-empty {
  padding: var(--space-2xl) var(--space-lg);
  text-align: center;
  background: var(--color-gray-50);
  border-radius: var(--radius-md);
  border: 1px dashed var(--color-border);
}

.comments-empty-text {
  margin: 0 0 var(--space-xs);
  color: var(--color-text-secondary);
  font-size: 0.9375rem;
}

.comments-empty-hint {
  margin: 0;
  color: var(--color-text-muted);
  font-size: 0.8125rem;
}
</style>
