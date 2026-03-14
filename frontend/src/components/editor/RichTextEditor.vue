<template>
  <div class="rich-text-editor">
    <div v-if="editor" class="editor-toolbar">
      <button
        type="button"
        class="toolbar-btn"
        :class="{ active: editor.isActive('bold') }"
        @click="editor.chain().focus().toggleBold().run()"
        title="Bold"
      >
        <strong>B</strong>
      </button>
      <button
        type="button"
        class="toolbar-btn"
        :class="{ active: editor.isActive('italic') }"
        @click="editor.chain().focus().toggleItalic().run()"
        title="Italic"
      >
        <em>I</em>
      </button>
      <button
        type="button"
        class="toolbar-btn"
        :class="{ active: editor.isActive('strike') }"
        @click="editor.chain().focus().toggleStrike().run()"
        title="Strikethrough"
      >
        <s>S</s>
      </button>
      <button
        type="button"
        class="toolbar-btn"
        :class="{ active: editor.isActive('bulletList') }"
        @click="editor.chain().focus().toggleBulletList().run()"
        title="Bullet list"
      >
        •
      </button>
      <button
        type="button"
        class="toolbar-btn"
        :class="{ active: editor.isActive('orderedList') }"
        @click="editor.chain().focus().toggleOrderedList().run()"
        title="Numbered list"
      >
        1.
      </button>
      <button
        type="button"
        class="toolbar-btn"
        :class="{ active: editor.isActive('heading', { level: 2 }) }"
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
        title="Heading"
      >
        H2
      </button>
      <span class="toolbar-divider" />
      <button
        type="button"
        class="toolbar-btn"
        :disabled="uploadingImage"
        title="Insert image"
        @click="triggerImageInput"
      >
        {{ uploadingImage ? '…' : '🖼' }}
      </button>
      <input
        ref="imageInputRef"
        type="file"
        accept="image/jpeg,image/png,image/gif,image/webp"
        class="sr-only"
        @change="onImageSelected"
      />
    </div>
    <EditorContent :editor="editor" class="editor-content" />
  </div>
</template>

<script setup>
/**
 * Rich text editor (TipTap) for post content. Supports bold, italic, lists, headings, and images.
 * Images are uploaded via API and the returned URL is inserted; v-model is HTML string.
 */
import { ref, watch, onBeforeUnmount } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Placeholder from '@tiptap/extension-placeholder'
import { postApi } from '@/api/posts'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Write your post content...' },
})

const emit = defineEmits(['update:modelValue'])

const imageInputRef = ref(null)
const uploadingImage = ref(false)

const editor = useEditor({
  content: props.modelValue || '',
  extensions: [
    StarterKit,
    Image.configure({ inline: false, allowBase64: false }),
    Placeholder.configure({ placeholder: props.placeholder }),
  ],
  editorProps: {
    attributes: {
      class: 'prose-editor',
    },
  },
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
})

// Sync external content into editor (e.g. when loading post for edit)
watch(
  () => props.modelValue,
  (val) => {
    if (editor.value && val !== editor.value.getHTML()) {
      editor.value.commands.setContent(val || '', false)
    }
  }
)

function triggerImageInput() {
  imageInputRef.value?.click()
}

/** Upload selected file to API and insert image node with returned URL */
async function onImageSelected(event) {
  const file = event.target.files?.[0]
  if (!file || !editor.value) return
  event.target.value = ''
  uploadingImage.value = true
  try {
    const { data } = await postApi.uploadImage(file)
    const url = data.url || data
    editor.value.chain().focus().setImage({ src: url }).run()
  } catch (err) {
    console.error('Image upload failed:', err)
  } finally {
    uploadingImage.value = false
  }
}

onBeforeUnmount(() => {
  editor.value?.destroy()
})
</script>

<style scoped>
.rich-text-editor {
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  overflow: hidden;
}

.editor-toolbar {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-sm);
  border-bottom: 1px solid var(--color-border);
  background: var(--color-gray-50);
}

.toolbar-btn {
  padding: var(--space-sm) var(--space-md);
  border: none;
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--color-text-secondary);
  cursor: pointer;
  font-size: 14px;
}

.toolbar-btn:hover:not(:disabled) {
  background: var(--color-border);
  color: var(--color-text-primary);
}

.toolbar-btn.active {
  background: var(--color-primary-lighter);
  color: var(--color-primary-dark);
}

.toolbar-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.toolbar-divider {
  width: 1px;
  height: 20px;
  background: var(--color-border);
  margin: 0 var(--space-xs);
}

.editor-content {
  min-height: 200px;
}

.editor-content :deep(.ProseMirror) {
  min-height: 200px;
  padding: var(--space-md);
  outline: none;
}

.editor-content :deep(.ProseMirror p.is-editor-empty:first-child::before) {
  content: attr(data-placeholder);
  float: left;
  color: var(--color-text-muted);
  pointer-events: none;
  height: 0;
}

.editor-content :deep(.ProseMirror img) {
  max-width: 100%;
  height: auto;
  border-radius: var(--radius-sm);
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
</style>
