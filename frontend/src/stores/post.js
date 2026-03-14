/**
 * Pinia store for posts: list, single post, create/update/delete, my posts.
 * Handles loading state and Laravel-style pagination (data.data, data.links, data.meta).
 */
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { postApi } from '@/api/posts'

export const usePostStore = defineStore('post', () => {
  const posts = ref([])
  const currentPost = ref(null)
  const myPosts = ref([])
  const loading = ref(false)
  const pagination = ref({ links: {}, meta: {} })

  /** Fetch paginated posts; updates posts and pagination */
  async function fetchPosts(params = {}) {
    loading.value = true
    try {
      const { data } = await postApi.getAll(params)
      posts.value = data.data ?? []
      pagination.value = {
        links: data.links ?? {},
        meta: data.meta ?? {},
      }
    } catch (error) {
      console.error('Failed to fetch posts:', error)
      posts.value = []
    } finally {
      loading.value = false
    }
  }

  /** Fetch one post by id; sets currentPost, throws on error */
  async function fetchPost(id) {
    loading.value = true
    try {
      const { data } = await postApi.getOne(id)
      currentPost.value = data.data ?? data
    } catch (error) {
      console.error('Failed to fetch post:', error)
      currentPost.value = null
      throw error
    } finally {
      loading.value = false
    }
  }

  /** Create post; returns the created post object */
  async function createPost(formData) {
    const { data } = await postApi.create(formData)
    return data.data ?? data
  }

  /** Update post by id; returns updated post */
  async function updatePost(id, formData) {
    const { data } = await postApi.update(id, formData)
    return data.data ?? data
  }

  /** Delete post by id (author only on backend) */
  async function deletePost(id) {
    await postApi.delete(id)
  }

  /** Fetch current user's posts; sets myPosts and pagination (params: page, per_page) */
  async function fetchMyPosts(params = {}) {
    loading.value = true
    try {
      const { data } = await postApi.getMyPosts(params)
      myPosts.value = data.data ?? []
      pagination.value = {
        links: data.links ?? {},
        meta: data.meta ?? {},
      }
    } catch (error) {
      console.error('Failed to fetch my posts:', error)
      myPosts.value = []
    } finally {
      loading.value = false
    }
  }

  /** Clear current post (e.g. when leaving detail page) */
  function clearCurrentPost() {
    currentPost.value = null
  }

  return {
    posts,
    currentPost,
    myPosts,
    loading,
    pagination,
    fetchPosts,
    fetchPost,
    createPost,
    updatePost,
    deletePost,
    fetchMyPosts,
    clearCurrentPost,
  }
})
