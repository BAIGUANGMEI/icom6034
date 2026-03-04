import api from './index'

export const searchApi = {
  searchPosts(params = {}) {
    return api.get('/search/posts', { params })
  },

  searchJobs(params = {}) {
    return api.get('/search/jobs', { params })
  },
}
