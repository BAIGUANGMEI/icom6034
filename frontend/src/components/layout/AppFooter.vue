<template>
  <footer class="app-footer">
    <p>&copy; {{ currentYear }} Job Sharing Platform. All rights reserved.</p>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>

<style scoped>
/* TODO: Add footer styles */
</style>
