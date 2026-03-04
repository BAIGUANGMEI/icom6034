import { defineStore } from 'pinia'
import { ref } from 'vue'
import { postApi } from '@/api/posts'

export const usePostStore = defineStore('post', () => {
  const posts = ref([])
  const currentPost = ref(null)
  const myPosts = ref([])
  const loading = ref(false)

  async function fetchPosts(params = {}) {
    // TODO: Implement fetch posts action
  }

  async function fetchPost(id) {
    // TODO: Implement fetch single post action
  }

  async function createPost(data) {
    // TODO: Implement create post action
  }

  async function updatePost(id, data) {
    // TODO: Implement update post action
  }

  async function deletePost(id) {
    // TODO: Implement delete post action
  }

  async function fetchMyPosts() {
    // TODO: Implement fetch my posts action
  }

  return {
    posts,
    currentPost,
    myPosts,
    loading,
    fetchPosts,
    fetchPost,
    createPost,
    updatePost,
    deletePost,
    fetchMyPosts,
  }
})
