<template>
  <footer class="footer">
    <div class="footer-inner container">
      <div class="footer-grid">
        <!-- Brand -->
        <div class="footer-brand">
          <router-link to="/" class="footer-logo">
            <svg viewBox="0 0 32 32" width="24" height="24" fill="none">
              <rect width="32" height="32" rx="6" fill="#42b883" />
              <text x="16" y="22" text-anchor="middle" font-size="16" font-weight="700" fill="#fff">J</text>
            </svg>
            <span>JobShare</span>
          </router-link>
          <p class="footer-desc">Share your interview experiences, job offers, and career-related discussions with the community.</p>
        </div>

        <!-- Quick Links -->
        <div class="footer-col">
          <h4>Platform</h4>
          <router-link to="/posts">Browse Posts</router-link>
          <router-link to="/search">Search Jobs</router-link>
          <router-link to="/news">Industry News</router-link>
        </div>

        <!-- Account -->
        <div class="footer-col">
          <h4>Account</h4>
          <router-link to="/login">Sign In</router-link>
          <router-link to="/register">Join Now</router-link>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; {{ currentYear }} JobShare. All rights reserved.</p>
        <p class="footer-built">Built with Vue.js &amp; Laravel</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>

<style scoped>
.footer {
  background: var(--color-gray-800);
  color: var(--color-gray-300);
  margin-top: auto;
}

.footer-inner {
  padding-top: var(--space-2xl);
  padding-bottom: var(--space-lg);
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: var(--space-2xl);
  padding-bottom: var(--space-xl);
  border-bottom: 1px solid var(--color-gray-700);
}

.footer-logo {
  display: inline-flex;
  align-items: center;
  gap: var(--space-sm);
  text-decoration: none;
  margin-bottom: var(--space-md);
}
.footer-logo span {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-white);
}

.footer-desc {
  font-size: 13px;
  line-height: 1.7;
  color: var(--color-gray-400);
  max-width: 300px;
}

.footer-col h4 {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-white);
  margin-bottom: var(--space-md);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.footer-col a {
  display: block;
  font-size: 13px;
  color: var(--color-gray-400);
  text-decoration: none;
  padding: var(--space-xs) 0;
  transition: color var(--transition-fast);
}
.footer-col a:hover {
  color: var(--color-primary-light);
}

.footer-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: var(--space-lg);
  font-size: 12px;
  color: var(--color-gray-500);
}

.footer-built {
  color: var(--color-gray-500);
}

@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: var(--space-xl);
  }
  .footer-bottom {
    flex-direction: column;
    gap: var(--space-sm);
    text-align: center;
  }
}
</style>
