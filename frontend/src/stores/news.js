import { defineStore } from 'pinia'
import { ref } from 'vue'
import { newsApi } from '@/api/news'

export const useNewsStore = defineStore('news', () => {
  const articles = ref([])
  const loading = ref(false)
  const error = ref('')
  const searched = ref(false)
  const totalResults = ref(0)
  const currentMode = ref('headlines')
  const currentQuery = ref('')

  async function fetchHeadlines(params = {}) {
    loading.value = true
    error.value = ''
    currentMode.value = 'headlines'
    currentQuery.value = ''

    try {
      const { data } = await newsApi.getHeadlines(params)
      articles.value = data.articles ?? []
      totalResults.value = data.totalResults ?? 0
      searched.value = false
    } catch (err) {
      console.error('Failed to fetch headlines:', err)
      articles.value = []
      totalResults.value = 0
      error.value = err.response?.data?.message || err.message || 'Failed to fetch headlines.'
    } finally {
      loading.value = false
    }
  }

  async function searchArticles(query, params = {}) {
    const keyword = query?.trim() || ''

    if (!keyword) {
      await fetchHeadlines(params)
      return
    }

    loading.value = true
    error.value = ''
    searched.value = true
    currentMode.value = 'search'
    currentQuery.value = keyword

    try {
      const { data } = await newsApi.search({
        q: keyword,
        ...params,
      })
      articles.value = data.articles ?? []
      totalResults.value = data.totalResults ?? 0
    } catch (err) {
      console.error('Failed to search news:', err)
      articles.value = []
      totalResults.value = 0
      error.value = err.response?.data?.message || err.message || 'Failed to search news.'
    } finally {
      loading.value = false
    }
  }

  return {
    articles,
    loading,
    error,
    searched,
    totalResults,
    currentMode,
    currentQuery,
    fetchHeadlines,
    searchArticles,
  }
})
