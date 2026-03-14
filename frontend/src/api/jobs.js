import axios from 'axios'

/**
 * JSearch API (RapidAPI) - job search. Called directly from frontend.
 * Base URL and API key from .env: VITE_JSEARCH_API_URL, VITE_JSEARCH_RAPIDAPI_KEY.
 */
const jsearchClient = axios.create({
  baseURL: import.meta.env.VITE_JSEARCH_API_URL || 'https://jsearch.p.rapidapi.com',
  headers: {
    'Content-Type': 'application/json',
    'X-RapidAPI-Host': 'jsearch.p.rapidapi.com',
    'X-RapidAPI-Key': import.meta.env.VITE_JSEARCH_RAPIDAPI_KEY || '',
  },
})

/**
 * Search jobs. Params: query, page, num_pages, country, date_posted (all|today|3days|week|month), etc.
 * Returns { status, request_id, parameters, data: Job[] }.
 */
export function searchJobs(params = {}) {
  return jsearchClient.get('/search', {
    params: {
      query: 'developer jobs',
      page: 1,
      num_pages: 1,
      country: 'us',
      date_posted: 'all',
      ...params,
    },
  })
}

export const jobsApi = { searchJobs }
