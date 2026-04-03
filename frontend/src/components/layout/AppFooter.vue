<template>
  <footer class="footer">
    <div class="footer-inner container">
      <div class="footer-grid">
        <div class="footer-brand">
          <router-link to="/" class="footer-logo">
            <svg viewBox="0 0 44 44" width="40" height="40" fill="none">
              <rect x="1" y="1" width="42" height="42" rx="16" fill="#F472B6" />
              <path d="M28 10l4 6-4 6-4-6 4-6Z" fill="#FBBF24" />
              <text x="16" y="28" text-anchor="middle" font-size="18" font-weight="800" fill="#fff">J</text>
            </svg>
            <span class="footer-logo-text">JobShare</span>
          </router-link>
          <p class="footer-desc">A bright corner of the internet for interview stories, career notes, helpful tags, and your next opportunity.</p>
          <div class="footer-badges">
            <span class="badge">Posts</span>
            <span class="badge footer-badge-alt">News</span>
            <span class="badge footer-badge-alt-2">Jobs</span>
          </div>
        </div>

        <div class="footer-col">
          <h4>Explore</h4>
          <router-link to="/posts">Browse Posts</router-link>
          <router-link to="/jobs">Search Jobs</router-link>
          <router-link to="/news">Industry News</router-link>
        </div>

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
  position: relative;
  margin-top: auto;
  padding: 0 0 var(--space-xl);
  color: var(--color-foreground);
}

.footer::before {
  content: '';
  position: absolute;
  inset: 18px 0 0;
  border-top: var(--border-width) dashed var(--color-border);
  opacity: 0.5;
}

.footer-inner {
  position: relative;
  padding-top: var(--space-3xl);
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.6fr 1fr 1fr;
  gap: var(--space-xl);
  padding: var(--space-2xl);
  background: transparent;
}

.footer-brand {
  max-width: 380px;
}

.footer-logo {
  display: inline-flex;
  align-items: center;
  gap: var(--space-md);
  margin-bottom: var(--space-md);
}

.footer-logo-text {
  font-family: 'Outfit', system-ui, sans-serif;
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--color-heading);
}

.footer-desc {
  font-size: 0.95rem;
  line-height: 1.75;
  color: var(--color-text-secondary);
  margin-bottom: var(--space-lg);
}

.footer-badges {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
}

.footer-badge-alt {
  background: var(--color-tertiary);
}

.footer-badge-alt-2 {
  background: var(--color-primary-lighter);
}

.footer-col h4 {
  margin-bottom: var(--space-md);
  font-size: 1rem;
  font-weight: 800;
  color: var(--color-heading);
}

.footer-col a {
  display: inline-flex;
  width: fit-content;
  padding: 8px 0;
  color: var(--color-text-secondary);
  font-size: 0.95rem;
  font-weight: 600;
}

.footer-col a:hover {
  color: var(--color-secondary);
  transform: translateX(2px);
}

.footer-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-md);
  padding: var(--space-lg) var(--space-md) 0;
  font-size: 0.82rem;
  color: var(--color-text-secondary);
}

.footer-built {
  font-family: 'Outfit', system-ui, sans-serif;
  font-weight: 700;
}

@media (max-width: 900px) {
  .footer-grid {
    grid-template-columns: 1fr;
  }

  .footer-brand {
    max-width: none;
  }
}

@media (max-width: 640px) {
  .footer-grid {
    padding: var(--space-xl);
  }

  .footer-bottom {
    flex-direction: column;
    text-align: center;
  }
}
</style>
