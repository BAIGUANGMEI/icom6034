import api from './index'

export const commentApi = {
  getByPost(postId) {
    return api.get(`/posts/${postId}/comments`)
  },

  create(postId, data) {
    return api.post(`/posts/${postId}/comments`, data)
  },

  delete(postId, commentId) {
    return api.delete(`/posts/${postId}/comments/${commentId}`)
  },
}
