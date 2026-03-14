/**
 * Comment API: list (flat with parent_id), create (optionally with parent_id for replies), delete.
 */
import api from './index'

export const commentApi = {
  getByPost(postId) {
    return api.get(`/posts/${postId}/comments`)
  },

  /** Create comment. Pass { content, parent_id? } to reply to another comment. */
  create(postId, data) {
    return api.post(`/posts/${postId}/comments`, data)
  },

  delete(postId, commentId) {
    return api.delete(`/posts/${postId}/comments/${commentId}`)
  },
}
