import api from './index'

export const tagApi = {
  getAll() {
    return api.get('/tags')
  },

  getOne(id) {
    return api.get(`/tags/${id}`)
  },
}
