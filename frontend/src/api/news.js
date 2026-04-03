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

function compactParams(params = {}) {
  return Object.fromEntries(
    Object.entries(params).filter(([, value]) => value !== '' && value != null),
  )
}

export const newsApi = {
  /**
   * Get top headlines.
   */
  getHeadlines(params = {}) {
    return newsClient.get('/top-headlines', {
      params: compactParams({
        country: 'us',
        category: 'technology',
        pageSize: 12,
        ...params,
      }),
    })
  },

  /**
   * Search news articles by keyword.
   */
  search(params = {}) {
    return newsClient.get(
      '/everything',
      {
        params: compactParams({
          q: 'technology',
          language: 'en',
          sortBy: 'publishedAt',
          pageSize: 12,
          ...params,
        }),
      },
    )
  },
}
