import axios from 'axios'

/**
 * News API (called directly from frontend)
 * API: https://newsapi.org/
 */
const newsClient = axios.create({
  baseURL: 'https://newsapi.org/v2',
  headers: {
    'X-Api-Key': import.meta.env.VITE_NEWS_API_KEY || '',
  },
})

export const newsApi = {
  /**
   * Get top headlines.
   */
  getHeadlines(params = {}) {
    return newsClient.get('/top-headlines', {
      params: { country: 'us', category: 'business', ...params },
    })
  },

  /**
   * Search news articles by keyword.
   */
  search(params = {}) {
    return newsClient.get('/everything', { params })
  },
}
