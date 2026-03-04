import api from './index'

export const newsApi = {
  getHeadlines(params = {}) {
    return api.get('/news', { params })
  },

  search(params = {}) {
    return api.get('/news/search', { params })
  },
}
