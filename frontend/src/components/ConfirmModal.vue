<template>
  <Teleport to="body">
    <Transition name="confirm-fade">
      <div
        v-if="open"
        class="confirm-overlay"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
        @keydown.esc="handleCancel"
      >
        <div class="confirm-backdrop" @click="handleCancel"></div>
        <div class="confirm-dialog card" @click.stop>
          <div class="confirm-badge" :class="`confirm-badge--${variant}`" aria-hidden="true"></div>
          <h2 id="confirm-modal-title" class="confirm-title">{{ title }}</h2>
          <p v-if="message" class="confirm-message">{{ message }}</p>
          <div class="confirm-actions">
            <button
              type="button"
              class="btn btn-ghost"
              :disabled="loading"
              @click.prevent="handleCancel"
            >
              {{ cancelText }}
            </button>
            <button
              type="button"
              :class="['btn', confirmButtonClass]"
              :disabled="loading"
              @click.prevent="handleConfirm"
            >
              <span v-if="loading" class="confirm-spinner" aria-hidden="true" />
              {{ loading ? loadingText : confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
/**
 * Reusable confirmation modal. Use v-model:open to control visibility.
 * @emits confirm - when user clicks confirm button
 * @emits cancel - when user clicks cancel or backdrop or Escape
 */
import { computed, watch } from 'vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: 'Confirm' },
  message: { type: String, default: '' },
  confirmText: { type: String, default: 'Confirm' },
  cancelText: { type: String, default: 'Cancel' },
  loading: { type: Boolean, default: false },
  loadingText: { type: String, default: 'Processing…' },
  /** 'danger' | 'primary' | 'default' - confirm button style */
  variant: { type: String, default: 'primary' },
})

const emit = defineEmits(['confirm', 'cancel', 'update:open'])

const confirmButtonClass = computed(() => {
  if (props.variant === 'danger') return 'btn-danger'
  if (props.variant === 'primary') return 'btn-primary'
  return 'btn-outline'
})

function handleConfirm() {
  emit('confirm')
}

function handleCancel() {
  if (!props.loading) {
    emit('cancel')
    emit('update:open', false)
  }
}

// Close on Escape; lock body scroll when open
watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      document.body.style.overflow = 'hidden'
      const onEsc = (e) => {
        if (e.key === 'Escape') handleCancel()
      }
      document.addEventListener('keydown', onEsc)
      return () => {
        document.removeEventListener('keydown', onEsc)
        document.body.style.overflow = ''
      }
    } else {
      document.body.style.overflow = ''
    }
  }
)
</script>

<style scoped>
.confirm-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-md);
  pointer-events: auto;
}

.confirm-backdrop {
  position: absolute;
  inset: 0;
  z-index: 0;
  background: rgba(15, 23, 42, 0.45);
  cursor: default;
}

.confirm-dialog {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 440px;
  padding: var(--space-xl);
  box-shadow: var(--shadow-soft);
  cursor: default;
}

.confirm-badge {
  width: 56px;
  height: 18px;
  margin-bottom: var(--space-lg);
  border: var(--border-width) solid var(--color-border);
  border-radius: var(--radius-full);
  background: var(--color-primary);
  transform: rotate(-8deg);
}

.confirm-badge--danger {
  background: var(--color-danger);
}

.confirm-badge--primary {
  background: var(--color-primary);
}

.confirm-badge--default {
  background: var(--color-tertiary);
}

.confirm-title {
  margin: 0 0 var(--space-md);
  font-size: 1.55rem;
  font-weight: 800;
  color: var(--color-heading);
}

.confirm-message {
  margin: 0 0 var(--space-lg);
  font-size: 0.9375rem;
  line-height: 1.5;
  color: var(--color-text-secondary);
}

.confirm-actions {
  display: flex;
  justify-content: flex-end;
  gap: var(--space-sm);
  flex-wrap: wrap;
}

.confirm-spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  margin-right: var(--space-xs);
  vertical-align: middle;
  border: 2px solid rgba(255, 255, 255, 0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: confirm-spin 0.6s linear infinite;
}

@keyframes confirm-spin {
  to {
    transform: rotate(360deg);
  }
}

.confirm-fade-enter-active,
.confirm-fade-leave-active {
  transition: opacity 0.2s ease;
}

.confirm-fade-enter-active .confirm-dialog,
.confirm-fade-leave-active .confirm-dialog {
  transition: transform 0.24s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.confirm-fade-enter-from,
.confirm-fade-leave-to {
  opacity: 0;
}

.confirm-fade-enter-from .confirm-dialog,
.confirm-fade-leave-to .confirm-dialog {
  transform: translateY(12px) scale(0.96);
}

@media (max-width: 640px) {
  .confirm-actions .btn {
    width: 100%;
  }
}
</style>
