<template>
  <div class="jobs-view">
    <div class="page-header">
      <h1 class="page-title">Jobs</h1>
      <p class="page-desc">Search jobs by keyword, country, date posted, and page.</p>
    </div>

    <form class="jobs-search card" @submit.prevent="runSearch">
      <div class="search-row">
        <div class="form-group">
          <label for="query">Query</label>
          <input
            id="query"
            v-model="form.query"
            type="text"
            class="form-input"
            placeholder="e.g. developer jobs in chicago"
          />
        </div>
        <div class="form-group form-group--sm">
          <label for="country">Country</label>
          <input
            id="country"
            v-model="form.country"
            type="text"
            class="form-input"
            placeholder="us"
          />
        </div>
        <div class="form-group form-group--sm">
          <label for="date_posted">Date posted</label>
          <select id="date_posted" v-model="form.date_posted" class="form-input">
            <option value="all">All</option>
            <option value="today">Today</option>
            <option value="3days">Past 3 days</option>
            <option value="week">Past week</option>
            <option value="month">Past month</option>
          </select>
        </div>
        <div class="form-group form-group--sm">
          <label for="page">Page</label>
          <input
            id="page"
            v-model.number="form.page"
            type="number"
            min="1"
            class="form-input"
          />
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Searching…' : 'Search' }}
          </button>
        </div>
      </div>
    </form>

    <div v-if="error" class="error-message card">
      {{ error }}
    </div>

    <div v-if="searched && !loading && jobs.length === 0" class="empty-state card">
      <p>No jobs found. Try different keywords or filters.</p>
    </div>

    <div v-else-if="jobs.length" class="jobs-list">
      <article v-for="(job, index) in jobs" :key="job.job_id || index" class="card job-card">
        <h2 class="job-title">{{ job.job_title || 'Untitled' }}</h2>
        <div class="job-meta">
          <span v-if="job.employer_name" class="job-employer">{{ job.employer_name }}</span>
          <span v-if="job.job_city || job.job_country" class="job-location">
            {{ [job.job_city, job.job_country].filter(Boolean).join(', ') }}
          </span>
          <span v-if="job.job_employment_type" class="job-type">{{ job.job_employment_type }}</span>
        </div>
        <p v-if="job.job_description" class="job-desc">{{ truncate(job.job_description, 200) }}</p>
        <div v-if="job.job_min_salary != null || job.job_max_salary != null" class="job-salary">
          <template v-if="job.job_min_salary != null && job.job_max_salary != null">
            {{ formatSalary(job.job_min_salary) }} – {{ formatSalary(job.job_max_salary) }}
          </template>
          <template v-else-if="job.job_min_salary != null">
            From {{ formatSalary(job.job_min_salary) }}
          </template>
          <template v-else>
            Up to {{ formatSalary(job.job_max_salary) }}
          </template>
        </div>
        <a
          v-if="job.job_apply_link"
          :href="job.job_apply_link"
          target="_blank"
          rel="noopener noreferrer"
          class="btn btn-primary btn-sm job-apply"
        >
          Apply
        </a>
      </article>
    </div>
  </div>
</template>

<script setup>
/**
 * Jobs page: search form (query, country, date_posted, page) and list of job cards from JSearch API.
 * API URL and RapidAPI key from .env (VITE_JSEARCH_API_URL, VITE_JSEARCH_RAPIDAPI_KEY).
 */
import { ref, reactive } from 'vue'
import { searchJobs } from '@/api/jobs'

const form = reactive({
  query: 'developer jobs in chicago',
  country: 'us',
  date_posted: 'all',
  page: 1,
})

const jobs = ref([])
const loading = ref(false)
const error = ref('')
const searched = ref(false)

function truncate(text, len) {
  if (!text) return ''
  const str = typeof text === 'string' ? text : String(text)
  return str.length > len ? str.slice(0, len) + '…' : str
}

function formatSalary(num) {
  if (num == null) return ''
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
  }).format(num)
}

async function runSearch() {
  loading.value = true
  error.value = ''
  searched.value = true
  try {
    const { data } = await searchJobs({
      query: form.query,
      country: form.country,
      date_posted: form.date_posted,
      page: form.page,
      num_pages: 1,
    })
    jobs.value = data?.data ?? []
  } catch (e) {
    jobs.value = []
    error.value = e.response?.data?.message || e.message || 'Failed to fetch jobs. Check your API key and .env.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.jobs-view {
  display: flex;
  flex-direction: column;
  gap: var(--space-xl);
}

.page-header {
  display: grid;
  gap: var(--space-xs);
}

.page-title {
  margin: 0;
  font-size: clamp(1.7rem, 2.4vw, 2.2rem);
}

.page-desc {
  margin: 0;
  color: var(--color-text-secondary);
}

.jobs-search {
  padding: var(--space-lg);
  background: transparent;
  border: none;
  box-shadow: none;
}

.search-row {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  gap: var(--space-md);
}

.form-group {
  flex: 1;
  min-width: 140px;
}

.form-group--sm {
  flex: 0 0 auto;
  width: 120px;
}

.form-group label {
  display: block;
  margin-bottom: var(--space-xs);
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-text-primary);
}

.form-input {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-size: 0.9375rem;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
}

.form-actions {
  flex-shrink: 0;
}

.error-message {
  padding: var(--space-md);
  margin-bottom: var(--space-lg);
  color: var(--color-danger);
  background: rgba(239, 68, 68, 0.08);
}

.empty-state {
  padding: var(--space-xl);
  text-align: center;
  color: var(--color-text-secondary);
}

.jobs-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
}

.job-card {
  padding: var(--space-lg);
  box-shadow: var(--shadow-soft);
  transition: transform var(--transition-normal), box-shadow var(--transition-normal);
}

.job-card:hover {
  transform: rotate(-1deg) translateY(-3px);
  box-shadow: var(--shadow-yellow);
}

.job-title {
  margin-bottom: var(--space-sm);
  font-size: 1.125rem;
  color: var(--color-heading);
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-md);
  margin-bottom: var(--space-sm);
  font-size: 0.875rem;
  color: var(--color-text-secondary);
}

.job-employer {
  font-weight: 700;
  color: var(--color-primary-dark);
}

.job-desc {
  margin-bottom: var(--space-md);
  font-size: 0.9375rem;
  line-height: 1.5;
  color: var(--color-text-secondary);
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.job-salary {
  margin-bottom: var(--space-md);
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-primary-dark);
}

.job-apply {
  display: inline-block;
}

</style>
