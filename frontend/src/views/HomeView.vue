<template>
  <div class="home-page">
    <section class="hero card">
      <div class="hero-copy">
        <h1 class="hero-title">
          Share Your <span class="hero-title-accent">Recruitment</span> Experiences
        </h1>
        <p class="hero-subtitle">
          Connect with professionals, share interview experiences, job offers, and career insights with a thriving community.
        </p>
        <div class="hero-actions">
          <router-link to="/posts" class="btn btn-primary btn-lg">Browse Posts</router-link>
          <router-link to="/register" class="btn btn-home-secondary btn-lg">Join the Community</router-link>
        </div>
      </div>
      <div class="hero-visual" aria-hidden="true">
        <div class="hero-id-card">
          <div class="id-card-layer id-card-layer--pink"></div>
          <div class="id-card-layer id-card-layer--green"></div>
          <div class="id-card-body">
            <div class="id-card-topband"></div>
            <div class="id-card-main">
              <div class="id-avatar"></div>
              <div class="id-lines">
                <div></div>
                <div></div>
                <div></div>
              </div>
            </div>
            <div class="id-card-chips">
              <span></span>
              <span></span>
              <span></span>
            </div>
            <div class="id-card-grid">
              <div></div>
              <div></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="features">
      <div class="features-header">
        <h2>Everything you need to level up your career</h2>
        <p class="text-secondary">Our platform keeps the content clean and readable, then adds just enough pop to make exploring feel fun.</p>
      </div>

      <div class="features-grid">
        <div class="feature-card card feature-card--violet">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm0 16H5V5h14v14ZM7 7h10v2H7V7Zm0 4h10v2H7v-2Zm0 4h7v2H7v-2Z"/></svg>
          </div>
          <h3>Share Experiences</h3>
          <p>Post your interview stories, offer details, and career tips. Help others learn from your journey.</p>
        </div>

        <div class="feature-card card feature-card--pink">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5Zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14Z"/></svg>
          </div>
          <h3>Search Opportunities</h3>
          <p>Discover job listings using tags and keywords that map naturally to what the community is already talking about.</p>
        </div>

        <div class="feature-card card feature-card--yellow">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 14H4V6h16v12ZM6 10h2v2H6v-2Zm0 4h2v2H6v-2Zm4-4h8v2h-8v-2Zm0 4h8v2h-8v-2Z"/></svg>
          </div>
          <h3>Industry News</h3>
          <p>Stay up to date with the latest recruitment trends and career-related news via curated feeds.</p>
        </div>

        <div class="feature-card card feature-card--green">
          <div class="feature-icon">
            <svg viewBox="0 0 24 24" width="28" height="28"><path fill="currentColor" d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1Zm-4 6V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10c.55 0 1-.45 1-1Z"/></svg>
          </div>
          <h3>Community Discussion</h3>
          <p>Engage through comments, share your thoughts, and get advice from industry professionals.</p>
        </div>
      </div>
    </section>

    <section class="cta card">
      <div>
        <h2>Ready to share your story?</h2>
      </div>
      <router-link to="/register" class="btn btn-primary btn-lg">Join the Community</router-link>
    </section>
  </div>
</template>

<script setup>
// TODO: Implement home view logic (e.g. fetch featured posts)
</script>

<style scoped>
.home-page {
  display: flex;
  flex-direction: column;
  gap: var(--space-3xl);
}

.hero {
  display: grid;
  grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
  gap: var(--space-2xl);
  align-items: center;
  overflow: hidden;
  padding: var(--space-3xl);
  background: transparent;
  border: none;
  box-shadow: none;
}

.hero-copy {
  position: relative;
  z-index: 1;
}

.hero-title {
  max-width: 14ch;
  margin-bottom: var(--space-md);
  font-size: clamp(2.7rem, 4.8vw, 4.6rem);
  font-weight: 800;
  line-height: 1.05;
}

.hero-title-accent {
  color: var(--color-secondary);
}

.hero-subtitle {
  max-width: 620px;
  margin-bottom: var(--space-xl);
  font-size: 1.04rem;
  color: var(--color-text-secondary);
  line-height: 1.7;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-md);
}

.btn-home-secondary {
  background: var(--color-secondary);
  color: #fff;
  box-shadow: var(--shadow-pop);
}

.btn-home-secondary:hover:not(:disabled) {
  background: #ec4899;
  color: #fff;
  box-shadow: var(--shadow-pop-hover);
}

.btn-home-secondary:active:not(:disabled) {
  box-shadow: var(--shadow-pop-active);
}

.hero-visual {
  position: relative;
}

.hero-id-card {
  position: relative;
  padding: var(--space-lg);
}

.id-card-body {
  position: relative;
  z-index: 2;
  border-radius: 32px;
  background: linear-gradient(145deg, #ffffff, rgba(255, 255, 255, 0.9));
  box-shadow: 0 24px 50px rgba(15, 23, 42, 0.12);
  padding: var(--space-xl);
}

.id-card-layer {
  position: absolute;
  inset: auto;
  border-radius: 32px;
  pointer-events: none;
}

.id-card-layer--pink {
  top: 18px;
  right: 0;
  width: 76%;
  height: 84%;
  background: linear-gradient(135deg, rgba(244, 114, 182, 0.9), rgba(251, 191, 36, 0.72));
  transform: rotate(8deg);
}

.id-card-layer--green {
  left: 10px;
  bottom: 10px;
  width: 72%;
  height: 72%;
  background: linear-gradient(135deg, rgba(52, 211, 153, 0.82), rgba(139, 92, 246, 0.6));
  transform: rotate(-10deg);
}

.id-card-topband {
  height: 18px;
  width: 42%;
  margin-bottom: var(--space-lg);
  border-radius: var(--radius-full);
  background: linear-gradient(90deg, var(--color-secondary), var(--color-tertiary));
}

.id-card-main {
  display: grid;
  grid-template-columns: 112px minmax(0, 1fr);
  gap: var(--space-lg);
  align-items: center;
  margin-bottom: var(--space-lg);
}

.id-avatar {
  width: 112px;
  height: 112px;
  border-radius: 50%;
  background:
    radial-gradient(circle at 50% 34%, rgba(255, 255, 255, 0.92) 0 16%, transparent 17%),
    linear-gradient(180deg, rgba(139, 92, 246, 0.9), rgba(52, 211, 153, 0.8));
  box-shadow: inset 0 -12px 24px rgba(255, 255, 255, 0.25);
}

.id-lines {
  display: grid;
  gap: 10px;
}

.id-lines div {
  height: 12px;
  border-radius: var(--radius-full);
  background: rgba(148, 163, 184, 0.28);
}

.id-lines div:nth-child(1) {
  width: 72%;
}

.id-lines div:nth-child(2) {
  width: 92%;
}

.id-lines div:nth-child(3) {
  width: 58%;
}

.id-card-chips {
  display: flex;
  gap: var(--space-sm);
  margin-bottom: var(--space-lg);
}

.id-card-chips span {
  height: 28px;
  border-radius: var(--radius-full);
}

.id-card-chips span:nth-child(1) {
  width: 28%;
  background: rgba(139, 92, 246, 0.2);
}

.id-card-chips span:nth-child(2) {
  width: 34%;
  background: rgba(244, 114, 182, 0.2);
}

.id-card-chips span:nth-child(3) {
  width: 22%;
  background: rgba(52, 211, 153, 0.22);
}

.id-card-grid {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: var(--space-md);
}

.id-card-grid div {
  min-height: 96px;
  border-radius: 24px;
}

.id-card-grid div:nth-child(1) {
  background: linear-gradient(135deg, rgba(251, 191, 36, 0.26), rgba(244, 114, 182, 0.16));
}

.id-card-grid div:nth-child(2) {
  background: linear-gradient(135deg, rgba(52, 211, 153, 0.26), rgba(139, 92, 246, 0.18));
}

.features-header {
  text-align: center;
  margin-bottom: var(--space-2xl);
}

.features-header h2 {
  max-width: none;
  white-space: nowrap;
  margin: 0 auto var(--space-md);
  font-size: clamp(1.55rem, 2vw, 2rem);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: var(--space-lg);
}

.feature-card {
  padding: var(--space-xl);
  transition:
    transform var(--transition-normal),
    box-shadow var(--transition-normal);
}

.feature-card:hover {
  transform: rotate(-1deg) translateY(-4px) scale(1.01);
}

.feature-card--violet {
  box-shadow: var(--shadow-soft);
}

.feature-card--pink {
  box-shadow: var(--shadow-pink);
}

.feature-card--yellow {
  box-shadow: var(--shadow-yellow);
}

.feature-card--green {
  box-shadow: 8px 8px 0 0 rgba(52, 211, 153, 0.5);
}

.feature-icon {
  width: 60px;
  height: 60px;
  margin-bottom: var(--space-md);
  border: var(--border-width) solid var(--color-border);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary);
  color: #fff;
  box-shadow: var(--shadow-pop);
}

.feature-card h3 {
  margin-bottom: var(--space-sm);
  font-size: 1.35rem;
}

.feature-card p {
  font-size: 0.98rem;
  line-height: 1.7;
  color: var(--color-text-secondary);
}

.cta {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-xl);
  padding: var(--space-2xl);
  background: linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(255, 255, 255, 0.96));
  box-shadow: var(--shadow-yellow);
  text-align: center;
}

.cta h2 {
  max-width: none;
  margin-bottom: 0;
  font-size: clamp(1.5rem, 2vw, 1.9rem);
  white-space: nowrap;
}

@media (max-width: 768px) {
  .hero {
    grid-template-columns: 1fr;
    padding: var(--space-xl);
  }

  .hero-title,
  .features-header h2,
  .cta h2 {
    max-width: none;
    white-space: normal;
  }

  .hero-visual {
    order: -1;
  }

  .hero-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .id-card-main {
    grid-template-columns: 1fr;
  }

  .id-avatar {
    margin: 0 auto;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

}

@media (max-width: 640px) {
  .hero-id-card {
    padding: var(--space-md);
  }
}
</style>
