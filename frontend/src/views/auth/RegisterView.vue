<template>
  <div class="auth-page">
    <div class="auth-card card">
      <!-- Brand -->
      <div class="auth-brand">
        <svg viewBox="0 0 32 32" width="40" height="40" fill="none">
          <rect width="32" height="32" rx="6" fill="#42b883" />
          <text x="16" y="22" text-anchor="middle" font-size="16" font-weight="700" fill="#fff">J</text>
        </svg>
        <h1 class="auth-title">Join JobShare</h1>
        <p class="text-secondary">Share and discover recruitment experiences</p>
      </div>

      <!-- Error banner -->
      <Transition name="fade">
        <div v-if="errorMessage" class="alert alert-error">
          <svg class="alert-icon" viewBox="0 0 20 20" width="18" height="18">
            <path fill="currentColor" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"/>
          </svg>
          <span>{{ errorMessage }}</span>
        </div>
      </Transition>

      <!-- Success banner -->
      <Transition name="fade">
        <div v-if="successMessage" class="alert alert-success">
          <svg class="alert-icon" viewBox="0 0 20 20" width="18" height="18">
            <path fill="currentColor" fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
          </svg>
          <span>{{ successMessage }}</span>
        </div>
      </Transition>

      <!-- Form -->
      <form @submit.prevent="handleRegister" novalidate>
        <!-- Name -->
        <div class="form-group">
          <label for="name" class="form-label">Name</label>
          <div class="input-wrapper">
            <svg class="input-icon" viewBox="0 0 20 20" width="18" height="18">
              <path fill="currentColor" fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z" clip-rule="evenodd"/>
            </svg>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="form-input has-icon"
              :class="{ 'input-error': errors.name || clientErrors.name }"
              placeholder="Enter your full name"
              autocomplete="name"
              @blur="validateName"
            />
          </div>
          <p v-if="clientErrors.name" class="form-error">{{ clientErrors.name }}</p>
          <p v-else-if="errors.name" class="form-error">{{ errors.name[0] }}</p>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <div class="input-wrapper">
            <svg class="input-icon" viewBox="0 0 20 20" width="18" height="18">
              <path fill="currentColor" d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/>
              <path fill="currentColor" d="m19 8.839-7.556 3.778a2.75 2.75 0 0 1-2.888 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/>
            </svg>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="form-input has-icon"
              :class="{ 'input-error': errors.email || clientErrors.email }"
              placeholder="Enter your email"
              autocomplete="email"
              @blur="validateEmail"
            />
          </div>
          <p v-if="clientErrors.email" class="form-error">{{ clientErrors.email }}</p>
          <p v-else-if="errors.email" class="form-error">{{ errors.email[0] }}</p>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="form-label">Password (6+ characters)</label>
          <div class="input-wrapper">
            <svg class="input-icon" viewBox="0 0 20 20" width="18" height="18">
              <path fill="currentColor" fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/>
            </svg>
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-input has-icon has-toggle"
              :class="{ 'input-error': errors.password || clientErrors.password }"
              placeholder="Create a password"
              autocomplete="new-password"
              @input="onPasswordInput"
              @blur="validatePassword"
            />
            <button type="button" class="toggle-password" @click="showPassword = !showPassword" tabindex="-1" :aria-label="showPassword ? 'Hide password' : 'Show password'">
              <svg v-if="!showPassword" viewBox="0 0 20 20" width="18" height="18"><path fill="currentColor" d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill="currentColor" fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
              <svg v-else viewBox="0 0 20 20" width="18" height="18"><path fill="currentColor" fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.745-1.745a10.029 10.029 0 0 0 3.3-4.38 1.651 1.651 0 0 0 0-1.185A10.004 10.004 0 0 0 9.999 3a9.956 9.956 0 0 0-4.744 1.194L3.28 2.22ZM7.752 6.69l1.092 1.092a2.5 2.5 0 0 1 3.374 3.373l1.092 1.092a4 4 0 0 0-5.558-5.558Z" clip-rule="evenodd"/><path fill="currentColor" d="m10.748 13.93 2.523 2.523a9.987 9.987 0 0 1-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 0 1 0-1.186A10.007 10.007 0 0 1 2.839 6.02L6.07 9.252a4 4 0 0 0 4.678 4.678Z"/></svg>
            </button>
          </div>
          <!-- Password strength indicator -->
          <div v-if="form.password" class="strength-bar-wrapper">
            <div class="strength-bar">
              <div class="strength-fill" :class="'strength-' + passwordStrength.level" :style="{ width: passwordStrength.percent + '%' }"></div>
            </div>
            <span class="strength-text" :class="'text-strength-' + passwordStrength.level">{{ passwordStrength.label }}</span>
          </div>
          <p v-if="clientErrors.password" class="form-error">{{ clientErrors.password }}</p>
          <p v-else-if="errors.password" class="form-error">{{ errors.password[0] }}</p>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <div class="input-wrapper">
            <svg class="input-icon" viewBox="0 0 20 20" width="18" height="18">
              <path fill="currentColor" fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/>
            </svg>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              :type="showConfirm ? 'text' : 'password'"
              class="form-input has-icon has-toggle"
              :class="{ 'input-error': errors.password_confirmation || clientErrors.password_confirmation }"
              placeholder="Confirm your password"
              autocomplete="new-password"
              @blur="validateConfirmation"
            />
            <button type="button" class="toggle-password" @click="showConfirm = !showConfirm" tabindex="-1" :aria-label="showConfirm ? 'Hide password' : 'Show password'">
              <svg v-if="!showConfirm" viewBox="0 0 20 20" width="18" height="18"><path fill="currentColor" d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill="currentColor" fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
              <svg v-else viewBox="0 0 20 20" width="18" height="18"><path fill="currentColor" fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.745-1.745a10.029 10.029 0 0 0 3.3-4.38 1.651 1.651 0 0 0 0-1.185A10.004 10.004 0 0 0 9.999 3a9.956 9.956 0 0 0-4.744 1.194L3.28 2.22ZM7.752 6.69l1.092 1.092a2.5 2.5 0 0 1 3.374 3.373l1.092 1.092a4 4 0 0 0-5.558-5.558Z" clip-rule="evenodd"/><path fill="currentColor" d="m10.748 13.93 2.523 2.523a9.987 9.987 0 0 1-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 0 1 0-1.186A10.007 10.007 0 0 1 2.839 6.02L6.07 9.252a4 4 0 0 0 4.678 4.678Z"/></svg>
            </button>
          </div>
          <!-- Match indicator -->
          <p v-if="form.password_confirmation && form.password === form.password_confirmation && !clientErrors.password_confirmation" class="form-hint form-hint-success">
            <svg viewBox="0 0 16 16" width="14" height="14"><path fill="currentColor" fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"/></svg>
            Passwords match
          </p>
          <p v-if="clientErrors.password_confirmation" class="form-error">{{ clientErrors.password_confirmation }}</p>
          <p v-else-if="errors.password_confirmation" class="form-error">{{ errors.password_confirmation[0] }}</p>
        </div>

        <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
          <span v-if="loading" class="spinner"></span>
          {{ loading ? 'Creating account...' : 'Agree & Join' }}
        </button>
      </form>

      <!-- Divider -->
      <div class="auth-divider">
        <span>or</span>
      </div>

      <!-- Footer -->
      <p class="auth-footer">
        Already on JobShare?
        <router-link to="/login" class="auth-link">Sign in</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const showPassword = ref(false)
const showConfirm = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})
const clientErrors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

// Password strength
const passwordStrength = computed(() => {
  const pw = form.password
  if (!pw) return { level: 0, label: '', percent: 0 }

  let score = 0
  if (pw.length >= 6) score++
  if (pw.length >= 10) score++
  if (/[A-Z]/.test(pw)) score++
  if (/[0-9]/.test(pw)) score++
  if (/[^A-Za-z0-9]/.test(pw)) score++

  if (score <= 1) return { level: 'weak', label: 'Weak', percent: 25 }
  if (score <= 2) return { level: 'fair', label: 'Fair', percent: 50 }
  if (score <= 3) return { level: 'good', label: 'Good', percent: 75 }
  return { level: 'strong', label: 'Strong', percent: 100 }
})

// Field validators
function validateName() {
  if (!form.name.trim()) {
    clientErrors.name = 'Name is required.'
  } else if (form.name.trim().length > 255) {
    clientErrors.name = 'Name must be 255 characters or less.'
  } else {
    clientErrors.name = ''
  }
}

function validateEmail() {
  if (!form.email) {
    clientErrors.email = 'Email is required.'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    clientErrors.email = 'Please enter a valid email address.'
  } else {
    clientErrors.email = ''
  }
}

function validatePassword() {
  if (!form.password) {
    clientErrors.password = 'Password is required.'
  } else if (form.password.length < 6) {
    clientErrors.password = 'Password must be at least 6 characters.'
  } else {
    clientErrors.password = ''
  }
  // Revalidate confirmation if it was already filled
  if (form.password_confirmation) {
    validateConfirmation()
  }
}

function onPasswordInput() {
  // Clear error as user types (if password becomes long enough)
  if (form.password.length >= 6) {
    clientErrors.password = ''
  }
}

function validateConfirmation() {
  if (!form.password_confirmation) {
    clientErrors.password_confirmation = 'Please confirm your password.'
  } else if (form.password !== form.password_confirmation) {
    clientErrors.password_confirmation = 'Passwords do not match.'
  } else {
    clientErrors.password_confirmation = ''
  }
}

function validateAll() {
  validateName()
  validateEmail()
  validatePassword()
  validateConfirmation()
  return !clientErrors.name && !clientErrors.email && !clientErrors.password && !clientErrors.password_confirmation
}

async function handleRegister() {
  if (!validateAll()) return

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''
  errors.value = {}

  try {
    await authStore.register(form)
    successMessage.value = 'Account created successfully! Redirecting...'
    setTimeout(() => {
      router.push({ name: 'Home' })
    }, 800)
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      errorMessage.value = error.response.data.message || 'Validation failed.'
    } else {
      errorMessage.value = 'Something went wrong. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 200px);
  padding: var(--space-xl) var(--space-md);
}

.auth-card {
  width: 100%;
  max-width: 420px;
  padding: var(--space-2xl);
}

.auth-brand {
  text-align: center;
  margin-bottom: var(--space-xl);
}

.auth-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: var(--space-sm) 0 var(--space-xs);
}

/* ===== Form ===== */
.form-group {
  margin-bottom: var(--space-md);
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: var(--space-xs);
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: var(--color-text-muted);
  pointer-events: none;
  flex-shrink: 0;
}

.form-input {
  width: 100%;
  padding: 10px 12px;
  font-size: 0.9375rem;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  color: var(--color-text-primary);
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
  box-sizing: border-box;
}

.form-input.has-icon {
  padding-left: 38px;
}

.form-input.has-toggle {
  padding-right: 42px;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-primary-lighter);
}

.form-input.input-error {
  border-color: var(--color-danger);
}

.form-input.input-error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
}

.toggle-password {
  position: absolute;
  right: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: none;
  color: var(--color-text-muted);
  cursor: pointer;
  border-radius: var(--radius-sm);
  transition: color var(--transition-fast), background var(--transition-fast);
}

.toggle-password:hover {
  color: var(--color-text-secondary);
  background: var(--color-background);
}

.form-error {
  font-size: 0.8125rem;
  color: var(--color-danger);
  margin-top: var(--space-xs);
}

.form-hint {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.8125rem;
  margin-top: var(--space-xs);
}

.form-hint-success {
  color: var(--color-success);
}

/* ===== Password Strength Bar ===== */
.strength-bar-wrapper {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-top: var(--space-xs);
}

.strength-bar {
  flex: 1;
  height: 4px;
  background: var(--color-border);
  border-radius: 2px;
  overflow: hidden;
}

.strength-fill {
  height: 100%;
  border-radius: 2px;
  transition: width 0.3s ease, background 0.3s ease;
}

.strength-weak   { background: var(--color-danger); }
.strength-fair    { background: var(--color-warning); }
.strength-good    { background: #60a5fa; }
.strength-strong  { background: var(--color-success); }

.strength-text {
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
}

.text-strength-weak   { color: var(--color-danger); }
.text-strength-fair    { color: var(--color-warning); }
.text-strength-good    { color: #3b82f6; }
.text-strength-strong  { color: var(--color-success); }

/* ===== Alert ===== */
.alert {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  margin-bottom: var(--space-md);
}

.alert-icon {
  flex-shrink: 0;
}

.alert-error {
  background: #fef2f2;
  color: var(--color-danger);
  border: 1px solid #fecaca;
}

.alert-success {
  background: #ecfdf5;
  color: var(--color-success);
  border: 1px solid #a7f3d0;
}

/* ===== Button ===== */
.btn-block {
  width: 100%;
  margin-top: var(--space-lg);
}

.spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  margin-right: var(--space-xs);
  vertical-align: middle;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ===== Divider ===== */
.auth-divider {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  margin: var(--space-lg) 0;
  color: var(--color-text-muted);
  font-size: 0.8125rem;
}

.auth-divider::before,
.auth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--color-border);
}

/* ===== Footer ===== */
.auth-footer {
  text-align: center;
  font-size: 0.875rem;
  color: var(--color-text-secondary);
}

.auth-link {
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
}

.auth-link:hover {
  text-decoration: underline;
}

/* ===== Transition ===== */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
