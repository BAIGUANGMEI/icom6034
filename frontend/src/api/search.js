import axios from 'axios'
import api from './index'

/**
 * LinkedIn Jobs API (called directly from frontend)
 * API: https://github.com/VishwaGauravIn/linkedin-jobs-api
 */
const linkedinApi = axios.create({
  baseURL: import.meta.env.VITE_LINKEDIN_JOBS_API_URL || 'https://linkedin-jobs-api.p.rapidapi.com',
  headers: {
    'X-RapidAPI-Key': import.meta.env.VITE_LINKEDIN_JOBS_API_KEY || '',
    'X-RapidAPI-Host': 'linkedin-jobs-api.p.rapidapi.com',
  },
})

export const searchApi = {
  /**
   * Search posts from backend (keyword / tag filtering).
   */
  searchPosts(params = {}) {
    return api.get('/search/posts', { params })
  },

  /**
   * Search jobs via LinkedIn Jobs API (external, no backend proxy).
   */
  searchJobs(params = {}) {
    return linkedinApi.get('/', { params })
  },
}
