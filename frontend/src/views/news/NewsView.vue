<template>
  <div class="news-view">
    <div class="page-header">
      <h1 class="page-title">News</h1>
      <p class="page-desc">Search by keyword or switch back to top headlines.</p>
    </div>

    <div v-if="!hasApiKey" class="warning-message card">
      Missing `VITE_NEWS_API_KEY` in `frontend/.env`. Add your News API key to enable this page.
    </div>

    <form class="news-search card" @submit.prevent="handleSearch">
      <div class="search-row">
        <div class="form-group">
          <label for="query">Keyword</label>
          <input
            id="query"
            v-model.trim="form.query"
            type="text"
            class="form-input"
            placeholder="e.g. AI, startup funding, Apple"
          />
        </div>

        <div class="form-group form-group--sm">
          <label for="sortBy">Sort by</label>
          <select id="sortBy" v-model="form.sortBy" class="form-input">
            <option value="publishedAt">Newest</option>
            <option value="popularity">Popularity</option>
            <option value="relevancy">Relevancy</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading || !hasApiKey">
            {{ loading ? 'Searching…' : 'Search' }}
          </button>
          <button
            type="button"
            class="btn btn-outline"
            :disabled="loading || !hasApiKey"
            @click="showHeadlines"
          >
            Top headlines
          </button>
        </div>
      </div>
    </form>

    <div v-if="error" class="error-message card">
      {{ error }}
    </div>

    <div v-if="loading" class="loading-state card">
      Loading news...
    </div>

    <div v-else class="results-section">
      <div class="section-header">
        <div>
          <h2 class="section-title">
            {{ currentMode === 'search' ? 'Search results' : 'Top headlines' }}
          </h2>
          <p class="section-subtitle">
            <template v-if="currentMode === 'search' && currentQuery">
              {{ totalResults }} result(s) for "{{ currentQuery }}"
            </template>
            <template v-else>
              Latest technology news from the US.
            </template>
          </p>
        </div>
      </div>

      <div v-if="articles.length === 0" class="empty-state card">
        <p>
          {{
            searched
              ? 'No news articles matched your keyword. Try a broader search.'
              : 'No headlines are available right now.'
          }}
        </p>
      </div>

      <div v-else class="news-list">
        <article v-for="(article, index) in articles" :key="article.url || index" class="card news-card">
          <img
            v-if="article.urlToImage"
            :src="article.urlToImage"
            :alt="article.title || 'News cover image'"
            class="news-image"
          />

          <div class="news-card-body">
            <div class="news-meta">
              <span v-if="article.source?.name" class="badge">{{ article.source.name }}</span>
              <span v-if="article.publishedAt">{{ formatDate(article.publishedAt) }}</span>
              <span v-if="article.author">{{ article.author }}</span>
            </div>

            <h3 class="news-title">
              <a :href="article.url" target="_blank" rel="noopener noreferrer">
                {{ article.title || 'Untitled article' }}
              </a>
            </h3>

            <p v-if="article.description" class="news-desc">
              {{ article.description }}
            </p>

            <a
              v-if="article.url"
              :href="article.url"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-primary btn-sm"
            >
              Read article
            </a>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useNewsStore } from '@/stores/news'

const newsStore = useNewsStore()
const { articles, loading, error, searched, totalResults, currentMode, currentQuery } =
  storeToRefs(newsStore)

const form = reactive({
  query: '',
  sortBy: 'publishedAt',
})

const hasApiKey = computed(() => Boolean(import.meta.env.VITE_NEWS_API_KEY))

function formatDate(value) {
  if (!value) return ''

  return new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}

async function handleSearch() {
  await newsStore.searchArticles(form.query, {
    sortBy: form.sortBy,
  })
}

async function showHeadlines() {
  form.query = ''
  await newsStore.fetchHeadlines()
}

onMounted(async () => {
  if (!hasApiKey.value) return
  await newsStore.fetchHeadlines()
})
</script>

<style scoped>
.news-view {
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

.news-search,
.warning-message,
.error-message,
.loading-state,
.empty-state {
  padding: var(--space-lg);
  margin-bottom: var(--space-lg);
}

.news-search {
  background: transparent;
  border: none;
  box-shadow: none;
}

.warning-message {
  color: var(--color-text-primary);
  background: var(--color-primary-lighter);
}

.error-message {
  color: var(--color-danger);
  background: rgba(239, 68, 68, 0.08);
}

.loading-state,
.empty-state {
  text-align: center;
  color: var(--color-text-secondary);
}

.search-row {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  gap: var(--space-md);
}

.form-group {
  flex: 1;
  min-width: 180px;
}

.form-group--sm {
  flex: 0 0 auto;
  width: 160px;
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
  box-shadow: 0 0 0 3px var(--color-primary-lighter);
}

.form-actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
}

.section-header {
  margin-bottom: var(--space-lg);
}

.section-title {
  margin-bottom: var(--space-xs);
  font-size: 1.4rem;
}

.section-subtitle {
  color: var(--color-text-secondary);
  font-size: 0.9375rem;
}

.news-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--space-lg);
}

.news-card {
  overflow: hidden;
  box-shadow: var(--shadow-soft);
  transition: transform var(--transition-normal), box-shadow var(--transition-normal);
}

.news-card:hover {
  transform: rotate(-1deg) translateY(-3px);
  box-shadow: var(--shadow-pink);
}

.news-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  display: block;
  background: var(--color-gray-100);
}

.news-card-body {
  padding: var(--space-lg);
}

.news-meta {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
  align-items: center;
  margin-bottom: var(--space-sm);
  font-size: 0.8125rem;
  color: var(--color-text-muted);
}

.news-title {
  margin-bottom: var(--space-sm);
  font-size: 1.0625rem;
  line-height: 1.4;
}

.news-title a {
  color: var(--color-heading);
}

.news-title a:hover {
  color: var(--color-secondary);
}

.news-desc {
  margin-bottom: var(--space-md);
  color: var(--color-text-secondary);
  line-height: 1.6;
}

@media (max-width: 640px) {
  .form-group,
  .form-group--sm,
  .form-actions {
    width: 100%;
  }

  .form-actions .btn {
    width: 100%;
  }
}
</style>
