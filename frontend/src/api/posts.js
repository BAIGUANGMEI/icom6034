/**
 * Post API: list, get, create, update, delete, my posts, and image upload.
 * Uses shared axios instance (auth token attached by interceptor).
 */
import api from './index'

export const postApi = {
  /** Paginated list; params: page, per_page */
  getAll(params = {}) {
    return api.get('/posts', { params })
  },

  /** Single post by id */
  getOne(id) {
    return api.get(`/posts/${id}`)
  },

  /** Create post; body: { title, content (HTML), tags: string[] } */
  create(data) {
    return api.post('/posts', data)
  },

  /** Update post; body: { title?, content?, tags? } */
  update(id, data) {
    return api.put(`/posts/${id}`, data)
  },

  /** Delete post (author only) */
  delete(id) {
    return api.delete(`/posts/${id}`)
  },

  /** Current user's posts (auth required); params: page, per_page */
  getMyPosts(params = {}) {
    return api.get('/my-posts', { params })
  },

  /** Upload image for post body. Returns { url }; use url in rich text content. */
  uploadImage(file) {
    const formData = new FormData()
    formData.append('image', file)
    return api.post('/posts/images', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },
}
