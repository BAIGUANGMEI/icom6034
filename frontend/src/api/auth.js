import api from './index'

export const authApi = {
  register(data) {
    return api.post('/register', data)
  },

  login(data) {
    return api.post('/login', data)
  },

  logout() {
    return api.post('/logout')
  },

  getUser() {
    return api.get('/user')
  },

  getProfile(id) {
    return api.get(`/users/${id}/profile`)
  },

  getUserPosts(id) {
    return api.get(`/users/${id}/posts`)
  },
}
