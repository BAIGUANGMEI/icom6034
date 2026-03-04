import api from './index'

export const postApi = {
  getAll(params = {}) {
    return api.get('/posts', { params })
  },

  getOne(id) {
    return api.get(`/posts/${id}`)
  },

  create(data) {
    return api.post('/posts', data)
  },

  update(id, data) {
    return api.put(`/posts/${id}`, data)
  },

  delete(id) {
    return api.delete(`/posts/${id}`)
  },

  getMyPosts() {
    return api.get('/my-posts')
  },
}
