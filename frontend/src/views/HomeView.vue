<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1 class="hero-title">Share Your <span class="highlight">Recruitment</span> Experiences</h1>
        <p class="hero-subtitle">
          Connect with professionals, share interview experiences, job offers, and career insights with a thriving community.
        </p>
        <div class="hero-actions">
          <router-link to="/posts" class="btn btn-primary btn-lg">Browse Posts</router-link>
          <router-link to="/register" class="btn btn-outline btn-lg">Join the Community</router-link>
        </div>
      </div>
      <div class="hero-illustration">
        <svg viewBox="0 0 400 300" fill="none" width="100%" style="max-width:420px">
          <!-- Stylized illustration -->
          <rect x="40" y="60" width="320" height="200" rx="16" fill="#f0faf5" stroke="#42b883" stroke-width="2"/>
          <rect x="70" y="100" width="140" height="16" rx="4" fill="#42b883" opacity="0.3"/>
          <rect x="70" y="130" width="260" height="10" rx="3" fill="#e5e7eb"/>
          <rect x="70" y="150" width="220" height="10" rx="3" fill="#e5e7eb"/>
          <rect x="70" y="170" width="240" height="10" rx="3" fill="#e5e7eb"/>
          <circle cx="310" cy="100" r="28" fill="#42b883" opacity="0.15"/>
          <circle cx="310" cy="100" r="14" fill="#42b883" opacity="0.4"/>
          <rect x="70" y="200" width="80" height="32" rx="16" fill="#42b883"/>
          <rect x="165" y="200" width="80" height="32" rx="16" fill="#e8f7f0" stroke="#42b883" stroke-width="1.5"/>
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="features-header">
        <h2>Everything you need to level up your career</h2>
        <p class="text-secondary">Our platform helps you navigate the job market with real insights from real people.</p>
      </div>

      <div class="features-grid">
        <div class="feature-card card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#42b883" d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm0 16H5V5h14v14ZM7 7h10v2H7V7Zm0 4h10v2H7v-2Zm0 4h7v2H7v-2Z"/></svg>
          </div>
          <h3>Share Experiences</h3>
          <p>Post your interview stories, offer details, and career tips. Help others learn from your journey.</p>
        </div>

        <div class="feature-card card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#42b883" d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5Zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14Z"/></svg>
          </div>
          <h3>Search Opportunities</h3>
          <p>Discover job listings and openings powered by LinkedIn's job search to find your next role.</p>
        </div>

        <div class="feature-card card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#42b883" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 14H4V6h16v12ZM6 10h2v2H6v-2Zm0 4h2v2H6v-2Zm4-4h8v2h-8v-2Zm0 4h8v2h-8v-2Z"/></svg>
          </div>
          <h3>Industry News</h3>
          <p>Stay up to date with the latest recruitment trends and career-related news via curated feeds.</p>
        </div>

        <div class="feature-card card">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#42b883" d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1Zm-4 6V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10c.55 0 1-.45 1-1Z"/></svg>
          </div>
          <h3>Community Discussion</h3>
          <p>Engage through comments, share your thoughts, and get advice from industry professionals.</p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta card">
      <h2>Ready to share your story?</h2>
      <p>Join thousands of professionals sharing their recruitment experiences.</p>
      <router-link to="/register" class="btn btn-primary btn-lg">Get Started — It's Free</router-link>
    </section>
  </div>
</template>

<script setup>
// TODO: Implement home view logic (e.g. fetch featured posts)
</script>

<style scoped>
/* ===== Hero ===== */
.hero {
  display: flex;
  align-items: center;
  gap: var(--space-2xl);
  padding: var(--space-2xl) 0;
}

.hero-content {
  flex: 1;
}

.hero-title {
  font-size: 40px;
  font-weight: 800;
  line-height: 1.2;
  color: var(--color-gray-900);
  margin-bottom: var(--space-md);
}
.highlight {
  color: var(--color-primary);
  position: relative;
}
.highlight::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 2px;
  width: 100%;
  height: 8px;
  background: var(--color-primary);
  opacity: 0.15;
  border-radius: 4px;
  z-index: -1;
}

.hero-subtitle {
  font-size: 17px;
  color: var(--color-text-secondary);
  line-height: 1.7;
  margin-bottom: var(--space-lg);
  max-width: 480px;
}

.hero-actions {
  display: flex;
  gap: var(--space-md);
}

.hero-illustration {
  flex: 1;
  display: flex;
  justify-content: center;
}

/* ===== Features ===== */
.features {
  padding: var(--space-2xl) 0;
}

.features-header {
  text-align: center;
  margin-bottom: var(--space-xl);
}
.features-header h2 {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-gray-900);
  margin-bottom: var(--space-sm);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--space-lg);
}

.feature-card {
  padding: var(--space-lg);
  transition: box-shadow var(--transition-normal), transform var(--transition-normal);
}
.feature-card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

.feature-icon {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-md);
  background: var(--color-primary-lighter);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--space-md);
}

.feature-card h3 {
  font-size: 17px;
  font-weight: 700;
  color: var(--color-gray-900);
  margin-bottom: var(--space-sm);
}
.feature-card p {
  font-size: 14px;
  color: var(--color-text-secondary);
  line-height: 1.7;
}

/* ===== CTA ===== */
.cta {
  text-align: center;
  padding: var(--space-2xl);
  margin: var(--space-xl) 0;
  background: linear-gradient(135deg, var(--color-primary-lighter) 0%, var(--color-surface) 100%);
  border-color: var(--color-primary);
}
.cta h2 {
  font-size: 26px;
  font-weight: 700;
  color: var(--color-gray-900);
  margin-bottom: var(--space-sm);
}
.cta p {
  font-size: 16px;
  color: var(--color-text-secondary);
  margin-bottom: var(--space-lg);
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .hero {
    flex-direction: column;
    text-align: center;
    padding: var(--space-xl) 0;
  }
  .hero-title {
    font-size: 28px;
  }
  .hero-subtitle {
    max-width: 100%;
  }
  .hero-actions {
    justify-content: center;
    flex-wrap: wrap;
  }
  .hero-illustration {
    order: -1;
  }
  .features-grid {
    grid-template-columns: 1fr;
  }
}
</style>
