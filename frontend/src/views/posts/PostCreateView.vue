<template>
  <div class="post-create-view container">
    <h1 class="page-title">Create New Post</h1>
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
          {{ submitting ? 'Publishing…' : 'Publish' }}
        </button>
        <router-link :to="{ name: 'Posts' }" class="btn btn-outline">Cancel</router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
/**
 * Create post page: title, rich text content (with image upload), and comma-separated tags.
 * Requires auth (route meta). On success redirects to post detail.
 */
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { usePostStore } from '@/stores/post'
import RichTextEditor from '@/components/editor/RichTextEditor.vue'

const router = useRouter()
const postStore = usePostStore()

const form = ref({ title: '', content: '' })
const tagsInput = ref('')
const errors = ref({})
const submitting = ref(false)

// Normalize tags from comma/space-separated string to array
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
    const post = await postStore.createPost(formData.value)
    router.push({ name: 'PostDetail', params: { id: post.id } })
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
.post-create-view {
  padding: var(--space-lg) var(--space-md);
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
