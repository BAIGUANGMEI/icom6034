<template>
  <div class="post-edit-view container">
    <div v-if="loading" class="loading">Loading…</div>
    <template v-else-if="post">
      <h1 class="page-title">Edit Post</h1>
      <form class="card post-form" @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="title">Title</label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            class="form-input"
            placeholder="Enter post title"
            maxlength="255"
          />
          <p v-if="errors.title" class="form-error">{{ errors.title[0] }}</p>
        </div>
        <div class="form-group">
          <label>Content (rich text & images)</label>
          <RichTextEditor v-model="form.content" placeholder="Share your experience..." />
          <p v-if="errors.content" class="form-error">{{ errors.content[0] }}</p>
        </div>
        <div class="form-group">
          <label for="tags">Tags (comma separated)</label>
          <input
            id="tags"
            v-model="tagsInput"
            type="text"
            class="form-input"
            placeholder="e.g. interview, google, experience"
          />
          <p v-if="errors.tags" class="form-error">{{ errors.tags[0] }}</p>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="submitting">
            {{ submitting ? 'Saving…' : 'Save' }}
          </button>
          <router-link :to="{ name: 'PostDetail', params: { id: postId } }" class="btn btn-outline">
            Cancel
          </router-link>
        </div>
      </form>
    </template>
    <p v-else class="text-muted">Post not found or you don't have permission to edit.</p>
  </div>
</template>

<script setup>
/**
 * Edit post page: loads post by route id, pre-fills form (title, rich text, tags).
 * Requires auth. Backend enforces author-only update. On success redirects to post detail.
 */
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { usePostStore } from '@/stores/post'
import RichTextEditor from '@/components/editor/RichTextEditor.vue'

const route = useRoute()
const router = useRouter()
const postStore = usePostStore()
const { currentPost, loading } = storeToRefs(postStore)

const postId = computed(() => route.params.id)
const post = computed(() => currentPost.value)

const form = ref({ title: '', content: '' })
const tagsInput = ref('')
const errors = ref({})
const submitting = ref(false)

// Populate form when currentPost is loaded (from store)
watch(
  currentPost,
  (p) => {
    if (p) {
      form.value = { title: p.title, content: p.content || '' }
      tagsInput.value = (p.tags || []).map((t) => t.name).join(', ')
    }
  },
  { immediate: true }
)

async function loadPost() {
  try {
    await postStore.fetchPost(postId.value)
  } catch {
    router.replace({ name: 'Posts' })
  }
}

loadPost()

const formData = computed(() => ({
  title: form.value.title.trim(),
  content: form.value.content || '',
  tags: tagsInput.value
    .split(/[,，\s]+/)
    .map((t) => t.trim())
    .filter(Boolean),
}))

async function handleSubmit() {
  if (formData.value.tags.length === 0) {
    errors.value = { tags: ['Please add at least one tag.'] }
    return
  }
  errors.value = {}
  submitting.value = true
  try {
    await postStore.updatePost(postId.value, formData.value)
    router.push({ name: 'PostDetail', params: { id: postId.value } })
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    }
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.post-edit-view {
  padding: var(--space-lg) var(--space-md);
}

.loading {
  padding: var(--space-xl);
  text-align: center;
  color: var(--color-text-secondary);
}

.page-title {
  margin-bottom: var(--space-lg);
  color: var(--color-heading);
  font-size: 1.5rem;
}

.post-form {
  padding: var(--space-xl);
  max-width: 720px;
}

.form-group {
  margin-bottom: var(--space-lg);
}

.form-group label {
  display: block;
  margin-bottom: var(--space-sm);
  font-weight: 500;
  color: var(--color-text-primary);
}

.form-input {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-size: 1rem;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 2px var(--color-primary-lighter);
}

.form-error {
  margin-top: var(--space-xs);
  font-size: 0.875rem;
  color: var(--color-danger);
}

.form-actions {
  display: flex;
  gap: var(--space-md);
  margin-top: var(--space-xl);
}
</style>
