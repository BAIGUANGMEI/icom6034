import api from './index'

/**
 * Search API — posts only (backend). No external job APIs.
 */
export const searchApi = {
  /**
   * Search posts from backend (title / tag filtering).
   */
  searchPosts(params = {}) {
    return api.get('/search/posts', { params })
  },
}
