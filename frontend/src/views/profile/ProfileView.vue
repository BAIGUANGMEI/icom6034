<template>
  <div class="profile-page">
    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner-lg"></div>
      <p class="text-secondary">Loading profile...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="error-state card">
      <svg viewBox="0 0 24 24" width="48" height="48"><path fill="var(--color-danger)" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm1 15h-2v-2h2v2Zm0-4h-2V7h2v6Z"/></svg>
      <h2>User not found</h2>
      <p class="text-secondary">The profile you're looking for doesn't exist.</p>
      <router-link to="/" class="btn btn-primary">Back to Home</router-link>
    </div>

    <!-- Profile Content -->
    <template v-else-if="profile">
      <!-- Profile Header Card -->
      <div class="profile-header card">
        <div class="profile-cover"></div>
        <div class="profile-info">
          <div class="profile-avatar">
            {{ profile.name?.charAt(0)?.toUpperCase() }}
          </div>
          <div class="profile-details">
            <h1 class="profile-name">{{ profile.name }}</h1>
            <p class="profile-email text-secondary">{{ profile.email }}</p>
            <p class="profile-joined text-muted">
              <svg viewBox="0 0 20 20" width="14" height="14"><path fill="currentColor" fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/></svg>
              Joined {{ formatDate(profile.created_at) }}
            </p>
          </div>
          <div v-if="isOwnProfile" class="profile-actions">
            <button class="btn btn-outline btn-sm" @click="showEditModal = true">
              <svg viewBox="0 0 20 20" width="16" height="16"><path fill="currentColor" d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z"/><path fill="currentColor" d="M3.5 5.75c0-.69.56-1.25 1.25-1.25h5a.75.75 0 0 0 0-1.5h-5A2.75 2.75 0 0 0 2 5.75v8.5A2.75 2.75 0 0 0 4.75 17h8.5A2.75 2.75 0 0 0 16 14.25v-5a.75.75 0 0 0-1.5 0v5c0 .69-.56 1.25-1.25 1.25h-8.5c-.69 0-1.25-.56-1.25-1.25v-8.5Z"/></svg>
              Edit Profile
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="profile-stats">
          <div class="stat-item">
            <span class="stat-value">{{ profile.posts_count }}</span>
            <span class="stat-label">Posts</span>
          </div>
          <div class="stat-item">
            <span class="stat-value">{{ profile.comments_count }}</span>
            <span class="stat-label">Comments</span>
          </div>
        </div>
      </div>

      <!-- Posts Section -->
      <div class="profile-posts">
        <h2 class="section-title">
          <svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm0 16H5V5h14v14ZM7 7h10v2H7V7Zm0 4h10v2H7v-2Zm0 4h7v2H7v-2Z"/></svg>
          Posts
        </h2>
        <p class="section-kicker text-secondary">A colorful archive of this user's tagged stories.</p>

        <div v-if="postsLoading" class="loading-state">
          <div class="spinner-lg"></div>
        </div>

        <div v-else-if="posts.length === 0" class="empty-state card">
          <svg viewBox="0 0 24 24" width="48" height="48"><path fill="var(--color-text-muted)" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm4 18H6V4h7v5h5v11Z"/></svg>
          <p class="text-secondary">No posts yet.</p>
        </div>

        <div v-else class="posts-list">
          <router-link
            v-for="post in posts"
            :key="post.id"
            :to="{ name: 'PostDetail', params: { id: post.id } }"
            class="post-card card"
          >
            <h3 class="post-title">{{ post.title }}</h3>
            <p class="post-excerpt">{{ excerpt(post.content) }}</p>
            <div class="post-meta">
              <div class="post-tags">
                <span v-for="tag in post.tags" :key="tag.id" class="badge">{{ tag.name }}</span>
              </div>
              <div class="post-info text-muted">
                <span>
                  <svg viewBox="0 0 20 20" width="14" height="14"><path fill="currentColor" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Z"/></svg>
                  {{ post.comments_count }} comments
                </span>
                <span>{{ formatDate(post.created_at) }}</span>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <div class="profile-jobs">
        <h2 class="section-title">
          <svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M10 3a2 2 0 0 0-2 2v1H6a3 3 0 0 0-3 3v2h18V9a3 3 0 0 0-3-3h-2V5a2 2 0 0 0-2-2h-4Zm4 3h-4V5h4v1Zm7 6H3v6a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3v-6Zm-9 2h2a1 1 0 1 1 0 2h-2a1 1 0 1 1 0-2Z"/></svg>
          Recommended Jobs
        </h2>
        <p class="section-kicker text-secondary">Based on the tags that show up most in this profile's posts.</p>

        <p v-if="recommendationTags.length" class="section-hint text-secondary">
          Based on tags:
          <span v-for="tag in recommendationTags" :key="tag" class="badge recommendation-tag">{{ tag }}</span>
        </p>

        <div v-if="!hasJobsApiKey" class="empty-state card">
          <p class="text-secondary">
            Add `VITE_JSEARCH_RAPIDAPI_KEY` in `frontend/.env` to enable job recommendations.
          </p>
        </div>

        <div v-else-if="jobsLoading" class="loading-state">
          <div class="spinner-lg"></div>
          <p class="text-secondary">Loading recommended jobs...</p>
        </div>

        <div v-else-if="jobsError" class="empty-state card">
          <p class="text-secondary">{{ jobsError }}</p>
        </div>

        <div v-else-if="posts.length === 0" class="empty-state card">
          <p class="text-secondary">Create some posts first to get job recommendations.</p>
        </div>

        <div v-else-if="recommendationTags.length === 0" class="empty-state card">
          <p class="text-secondary">Add tags to posts to unlock related job recommendations.</p>
        </div>

        <div v-else-if="recommendedJobs.length === 0" class="empty-state card">
          <p class="text-secondary">No matching jobs were found for these tags.</p>
        </div>

        <div v-else class="jobs-list">
          <article v-for="(job, index) in recommendedJobs" :key="job.job_id || index" class="card job-card">
            <h3 class="job-title">{{ job.job_title || 'Untitled job' }}</h3>
            <div class="job-meta">
              <span v-if="job.employer_name" class="job-employer">{{ job.employer_name }}</span>
              <span v-if="job.job_city || job.job_country">
                {{ [job.job_city, job.job_country].filter(Boolean).join(', ') }}
              </span>
              <span v-if="job.job_employment_type">{{ job.job_employment_type }}</span>
            </div>
            <p v-if="job.job_description" class="job-desc">{{ truncate(job.job_description, 180) }}</p>
            <a
              v-if="job.job_apply_link"
              :href="job.job_apply_link"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-primary btn-sm"
            >
              Apply
            </a>
          </article>
        </div>
      </div>
    </template>

    <!-- Edit Profile Modal -->
    <Transition name="fade">
      <div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false">
        <div class="modal card">
          <div class="modal-header">
            <h2>Edit Profile</h2>
            <button class="modal-close" @click="showEditModal = false" aria-label="Close">
              <svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41Z"/></svg>
            </button>
          </div>

          <form @submit.prevent="handleUpdateProfile" novalidate>
            <div class="form-group">
              <label for="edit-name" class="form-label">Name</label>
              <input
                id="edit-name"
                v-model="editForm.name"
                type="text"
                class="form-input"
                :class="{ 'input-error': editErrors.name }"
                placeholder="Your name"
              />
              <p v-if="editErrors.name" class="form-error">{{ editErrors.name[0] }}</p>
            </div>

            <div class="form-group">
              <label for="edit-email" class="form-label">Email</label>
              <input
                id="edit-email"
                v-model="editForm.email"
                type="email"
                class="form-input"
                :class="{ 'input-error': editErrors.email }"
                placeholder="Your email"
              />
              <p v-if="editErrors.email" class="form-error">{{ editErrors.email[0] }}</p>
            </div>

            <div class="modal-actions">
              <button type="button" class="btn btn-ghost" @click="showEditModal = false">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="editLoading">
                <span v-if="editLoading" class="spinner"></span>
                {{ editLoading ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { authApi } from '@/api/auth'
import api from '@/api/index'
import { searchJobs } from '@/api/jobs'

const route = useRoute()
const authStore = useAuthStore()

const profile = ref(null)
const posts = ref([])
const loading = ref(true)
const postsLoading = ref(true)
const error = ref(false)
const recommendedJobs = ref([])
const recommendationTags = ref([])
const jobsLoading = ref(false)
const jobsError = ref('')

// Edit modal
const showEditModal = ref(false)
const editForm = ref({ name: '', email: '' })
const editErrors = ref({})
const editLoading = ref(false)

const isOwnProfile = computed(() => {
  return authStore.user && profile.value && authStore.user.id === profile.value.id
})

const hasJobsApiKey = computed(() => Boolean(import.meta.env.VITE_JSEARCH_RAPIDAPI_KEY))

function formatDate(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

/** Strip HTML and take first 150 chars for plain-text excerpt (post content is rich text). */
function excerpt(html) {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  const text = (div.textContent || div.innerText || '').trim()
  return text.length > 150 ? text.slice(0, 150) + '…' : text
}

function truncate(text, len) {
  if (!text) return ''
  const str = typeof text === 'string' ? text : String(text)
  return str.length > len ? str.slice(0, len) + '…' : str
}

function getTopTags(postList) {
  const counts = new Map()

  postList.forEach((post) => {
    post.tags?.forEach((tag) => {
      const name = tag?.name?.trim()
      if (!name) return
      counts.set(name, (counts.get(name) || 0) + 1)
    })
  })

  return [...counts.entries()]
    .sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]))
    .slice(0, 3)
    .map(([name]) => name)
}

async function fetchRecommendedJobs(postList) {
  recommendationTags.value = getTopTags(postList)
  recommendedJobs.value = []
  jobsError.value = ''

  if (!hasJobsApiKey.value || recommendationTags.value.length === 0) {
    return
  }

  jobsLoading.value = true

  try {
    const query = `${recommendationTags.value.join(' OR ')} jobs`
    const { data } = await searchJobs({
      query,
      page: 1,
      num_pages: 1,
      country: 'us',
      date_posted: 'month',
    })

    recommendedJobs.value = (data?.data ?? []).slice(0, 6)
  } catch (e) {
    recommendedJobs.value = []
    jobsError.value =
      e.response?.data?.message || e.message || 'Failed to fetch related job recommendations.'
  } finally {
    jobsLoading.value = false
  }
}

async function fetchProfile(id) {
  loading.value = true
  error.value = false
  try {
    const { data } = await authApi.getProfile(id)
    profile.value = data.data
    editForm.value = { name: data.data.name, email: data.data.email }
  } catch (e) {
    error.value = true
  } finally {
    loading.value = false
  }
}

async function fetchUserPosts(id) {
  postsLoading.value = true
  try {
    const { data } = await authApi.getUserPosts(id)
    posts.value = data.data
    await fetchRecommendedJobs(posts.value)
  } catch (e) {
    posts.value = []
    recommendationTags.value = []
    recommendedJobs.value = []
    jobsError.value = ''
  } finally {
    postsLoading.value = false
  }
}

async function handleUpdateProfile() {
  editLoading.value = true
  editErrors.value = {}
  try {
    const { data } = await api.put('/user/profile', editForm.value)
    profile.value = { ...profile.value, ...data.data }
    authStore.user = { ...authStore.user, name: data.data.name, email: data.data.email }
    showEditModal.value = false
  } catch (e) {
    if (e.response?.status === 422) {
      editErrors.value = e.response.data.errors || {}
    }
  } finally {
    editLoading.value = false
  }
}

function loadProfile() {
  const id = route.params.id
  fetchProfile(id)
  fetchUserPosts(id)
}

onMounted(loadProfile)

watch(() => route.params.id, (newId) => {
  if (newId) loadProfile()
})
</script>

<style scoped>
.profile-page {
  max-width: 980px;
  margin: 0 auto;
}

.loading-state,
.empty-state,
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-md);
  padding: var(--space-2xl);
  text-align: center;
}

.spinner-lg {
  width: 32px;
  height: 32px;
  border: 3px solid var(--color-border);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.profile-header {
  overflow: hidden;
  margin-bottom: var(--space-2xl);
  box-shadow: var(--shadow-soft);
}

.profile-cover {
  position: relative;
  height: 150px;
  background:
    radial-gradient(circle at 22% 35%, rgba(255, 255, 255, 0.5) 0 12%, transparent 13%),
    linear-gradient(135deg, var(--color-primary), var(--color-secondary));
}

.profile-info {
  display: flex;
  align-items: flex-start;
  gap: var(--space-lg);
  padding: 0 var(--space-lg) var(--space-lg);
  margin-top: -36px;
  position: relative;
}

.profile-avatar {
  width: 76px;
  height: 76px;
  border-radius: 50%;
  border: var(--border-width) solid var(--color-border);
  background: var(--color-quaternary);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Outfit', system-ui, sans-serif;
  font-weight: 800;
  font-size: 1.8rem;
  flex-shrink: 0;
  box-shadow: var(--shadow-pop);
}

.profile-details {
  flex: 1;
  padding-top: 40px;
}

.profile-name {
  margin: 0 0 var(--space-xs);
  font-size: 2rem;
}

.profile-email {
  margin: 0 0 var(--space-xs);
}

.profile-joined {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.8125rem;
  margin: 0;
}

.profile-actions {
  padding-top: 40px;
  flex-shrink: 0;
}

.profile-stats {
  display: flex;
  border-top: var(--border-width) dashed var(--color-border-soft);
}

.stat-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  padding: var(--space-md);
}

.stat-item + .stat-item {
  border-left: var(--border-width) dashed var(--color-border-soft);
}

.stat-value {
  font-size: 1.75rem;
  font-family: 'Outfit', system-ui, sans-serif;
  font-weight: 800;
  color: var(--color-primary-dark);
}

.stat-label {
  font-size: 0.78rem;
  color: var(--color-text-muted);
  font-family: 'Outfit', system-ui, sans-serif;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.section-title {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-bottom: var(--space-sm);
  font-size: 1.5rem;
}

.section-kicker {
  margin-bottom: var(--space-lg);
}

.posts-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
}

.profile-jobs {
  margin-top: var(--space-2xl);
}

.section-hint {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-sm);
  margin: 0 0 var(--space-md);
}

.recommendation-tag {
  margin-left: 0;
}

.post-card {
  display: block;
  padding: var(--space-lg);
  box-shadow: var(--shadow-soft);
  transition: box-shadow var(--transition-fast), transform var(--transition-fast);
}

.post-card:hover {
  box-shadow: var(--shadow-pink);
  transform: rotate(-1deg) translateY(-2px);
}

.post-title {
  margin: 0 0 var(--space-sm);
  font-size: 1.2rem;
}

.post-card:hover .post-title {
  color: var(--color-secondary);
}

.post-excerpt {
  margin: 0 0 var(--space-md);
  color: var(--color-text-secondary);
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.post-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-sm);
}

.post-tags {
  display: flex;
  gap: var(--space-xs);
  flex-wrap: wrap;
}

.post-info {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  font-size: 0.8125rem;
}

.post-info span {
  display: flex;
  align-items: center;
  gap: 4px;
}

.jobs-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: var(--space-md);
}

.job-card {
  padding: var(--space-lg);
  box-shadow: var(--shadow-soft);
}

.job-title {
  margin: 0 0 var(--space-sm);
  font-size: 1.05rem;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
  margin-bottom: var(--space-sm);
  font-size: 0.8125rem;
  color: var(--color-text-muted);
}

.job-employer {
  font-weight: 700;
  color: var(--color-primary-dark);
}

.job-desc {
  margin: 0 0 var(--space-md);
  color: var(--color-text-secondary);
  font-size: 0.875rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: var(--space-md);
}

.modal {
  width: 100%;
  max-width: 460px;
  padding: var(--space-xl);
  box-shadow: var(--shadow-soft);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-lg);
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.modal-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: var(--border-width) solid transparent;
  background: none;
  color: var(--color-text-muted);
  border-radius: 50%;
}

.modal-close:hover {
  background: var(--color-primary-lighter);
  color: var(--color-text-primary);
  border-color: var(--color-border);
}

.form-group {
  margin-bottom: var(--space-md);
}

.form-label {
  display: block;
  margin-bottom: var(--space-xs);
  font-family: 'Outfit', system-ui, sans-serif;
  font-size: 0.78rem;
  font-weight: 800;
  color: var(--color-text-primary);
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.form-input {
  width: 100%;
}

.form-input.input-error {
  border-color: var(--color-danger);
}

.form-error {
  font-size: 0.8125rem;
  color: var(--color-danger);
  margin-top: var(--space-xs);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: var(--space-sm);
  margin-top: var(--space-lg);
}

.spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin-right: var(--space-xs);
  vertical-align: middle;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 640px) {
  .profile-info {
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 0 var(--space-md) var(--space-md);
    margin-top: -30px;
  }

  .profile-details {
    padding-top: var(--space-sm);
  }

  .profile-actions {
    padding-top: 0;
  }

  .profile-joined {
    justify-content: center;
  }

  .post-meta {
    flex-direction: column;
    align-items: flex-start;
  }

  .modal-actions .btn {
    width: 100%;
  }
}
</style>
