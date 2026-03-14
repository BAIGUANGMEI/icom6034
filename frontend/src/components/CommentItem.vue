<template>
  <li class="comment-item" :class="{ 'comment-item--reply': isReply }">
    <div class="comment-item-inner">
      <div class="comment-avatar" :aria-hidden="true">
        {{ (comment.user?.name || '?').charAt(0).toUpperCase() }}
      </div>
      <div class="comment-body">
        <div class="comment-meta">
          <span v-if="comment.parent?.user" class="reply-to">Reply to <strong>{{ comment.parent.user.name }}</strong></span>
          <router-link :to="{ name: 'Profile', params: { id: comment.user?.id } }" class="comment-author">
            {{ comment.user?.name }}
          </router-link>
          <span class="comment-date">{{ formatDate(comment.created_at) }}</span>
          <span class="comment-actions">
            <button
              v-if="authStore.isAuthenticated"
              type="button"
              class="btn-ghost-sm"
              @click="$emit('reply-to', comment.id)"
            >
              Reply
            </button>
            <button
              v-if="canDeleteComment(comment)"
              type="button"
              class="btn-ghost-sm btn-ghost-sm--danger"
              @click="$emit('delete', comment.id)"
            >
              Delete
            </button>
          </span>
        </div>
        <p class="comment-content">{{ comment.content }}</p>
        <div v-if="authStore.isAuthenticated && replyingToId === comment.id" class="reply-form">
          <textarea
            :value="replyContent"
            placeholder="Write a reply…"
            rows="2"
            class="reply-form-input"
            @input="$emit('update:replyContent', ($event.target).value)"
          />
          <div class="reply-actions">
            <button
              type="button"
              class="btn btn-primary btn-sm"
              :disabled="!replyContent.trim() || submittingReply"
              @click="$emit('submit-reply', comment.id)"
            >
              {{ submittingReply ? 'Submitting…' : 'Reply' }}
            </button>
            <button type="button" class="btn btn-ghost btn-sm" @click="$emit('cancel-reply')">Cancel</button>
          </div>
        </div>
        <button
          v-if="hasReplies"
          type="button"
          class="replies-toggle"
          :aria-expanded="!repliesCollapsed"
          @click="repliesCollapsed = !repliesCollapsed"
        >
          <span class="replies-toggle-icon" :class="{ 'replies-toggle-icon--collapsed': repliesCollapsed }">▼</span>
          {{ repliesCollapsed ? `Show replies (${replyCount})` : `Hide replies (${replyCount})` }}
        </button>
      </div>
    </div>
    <ul v-show="!repliesCollapsed" v-if="hasReplies" class="comment-list comment-replies">
      <CommentItem
        v-for="r in comment.replies"
        :key="r.id"
        :comment="r"
        :replying-to-id="replyingToId"
        :reply-content="replyContent"
        :submitting-reply="submittingReply"
        :can-delete-comment="canDeleteComment"
        :format-date="formatDate"
        @reply-to="$emit('reply-to', $event)"
        @submit-reply="$emit('submit-reply', $event)"
        @cancel-reply="$emit('cancel-reply')"
        @update:replyContent="$emit('update:replyContent', $event)"
        @delete="$emit('delete', $event)"
      />
    </ul>
  </li>
</template>

<script setup>
/**
 * Recursive comment item: shows one comment, optional reply form, and nested replies (collapsible).
 */
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  comment: { type: Object, required: true },
  replyingToId: { type: Number, default: null },
  replyContent: { type: String, default: '' },
  submittingReply: { type: Boolean, default: false },
  canDeleteComment: { type: Function, required: true },
  formatDate: { type: Function, required: true },
  isReply: { type: Boolean, default: false },
})

defineEmits(['reply-to', 'submit-reply', 'cancel-reply', 'update:replyContent', 'delete'])

const authStore = useAuthStore()
const repliesCollapsed = ref(false)

const hasReplies = computed(() => (props.comment.replies?.length ?? 0) > 0)
const replyCount = computed(() => props.comment.replies?.length ?? 0)
</script>

<style scoped>
.comment-item {
  transition: background var(--transition-fast);
}

.comment-item:hover .comment-item-inner {
  background: var(--color-gray-50);
  border-radius: var(--radius-md);
}

.comment-item-inner {
  display: flex;
  gap: var(--space-md);
  padding: var(--space-md) 0;
}

.comment-item--reply .comment-item-inner {
  padding: var(--space-sm) 0;
}

.comment-avatar {
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary-lighter);
  color: var(--color-primary-dark);
  border-radius: var(--radius-full);
  font-size: 0.875rem;
  font-weight: 600;
}

.comment-item--reply .comment-avatar {
  width: 28px;
  height: 28px;
  font-size: 0.75rem;
}

.comment-body {
  flex: 1;
  min-width: 0;
}

.comment-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-sm);
  margin-bottom: var(--space-sm);
  font-size: 0.875rem;
}

.comment-author {
  font-weight: 600;
  color: var(--color-primary);
  text-decoration: none;
  transition: color var(--transition-fast);
}

.comment-author:hover {
  color: var(--color-primary-dark);
  text-decoration: underline;
}

.comment-date {
  color: var(--color-text-muted);
  font-size: 0.8125rem;
}

.comment-content {
  margin: 0;
  font-size: 0.9375rem;
  line-height: 1.6;
  color: var(--color-text-primary);
}

.comment-item--reply .comment-content {
  font-size: 0.875rem;
}

.reply-to {
  margin-right: var(--space-sm);
  color: var(--color-text-muted);
  font-size: 0.8125rem;
}

.reply-to strong {
  color: var(--color-text-secondary);
  font-weight: 500;
}

.comment-actions {
  margin-left: auto;
  display: inline-flex;
  gap: var(--space-xs);
}

.btn-ghost-sm {
  padding: var(--space-xs) var(--space-sm);
  border: none;
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--color-text-muted);
  cursor: pointer;
  font-size: 0.8125rem;
  font-weight: 500;
  transition: color var(--transition-fast), background var(--transition-fast);
}

.btn-ghost-sm:hover {
  color: var(--color-primary);
  background: var(--color-primary-lighter);
}

.reply-form {
  margin-top: var(--space-md);
  padding: var(--space-md);
  background: var(--color-gray-50);
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
}

.reply-form-input {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  margin-bottom: var(--space-sm);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  font-size: 0.875rem;
  line-height: 1.5;
  resize: vertical;
  min-height: 60px;
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
}

.reply-form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 2px var(--color-primary-lighter);
}

.reply-form-input::placeholder {
  color: var(--color-text-muted);
}

.reply-actions {
  display: flex;
  gap: var(--space-sm);
}

.replies-toggle {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  margin-top: var(--space-sm);
  padding: var(--space-xs) 0;
  border: none;
  background: none;
  color: var(--color-primary);
  font-size: 0.8125rem;
  font-weight: 500;
  cursor: pointer;
  transition: color var(--transition-fast);
}

.replies-toggle:hover {
  color: var(--color-primary-dark);
  text-decoration: underline;
}

.replies-toggle-icon {
  font-size: 0.625rem;
  transition: transform var(--transition-fast);
}

.replies-toggle-icon--collapsed {
  transform: rotate(-90deg);
}

.comment-replies {
  margin-top: var(--space-sm);
  margin-left: var(--space-xl);
  padding-left: var(--space-lg);
  border-left: 3px solid var(--color-primary-lighter);
  border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
}

.comment-item--reply .comment-replies {
  margin-left: calc(var(--space-xl) - 8px);
  padding-left: var(--space-md);
  border-left-width: 2px;
}

.btn-ghost-sm--danger:hover {
  color: var(--color-danger) !important;
  background: rgba(239, 68, 68, 0.08) !important;
}
</style>
