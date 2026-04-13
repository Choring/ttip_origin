<template>
  <div v-if="editor" class="border border-gray-300 rounded-lg overflow-hidden flex flex-col focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all">
    
    <!-- Toolbar -->
    <div class="bg-gray-50 border-b border-gray-300 p-2 flex flex-wrap gap-1 sticky top-0 z-10">
      <!-- Bold -->
      <button 
        type="button"
        @click="editor.chain().focus().toggleBold().run()" 
        :class="{ 'bg-indigo-100 text-indigo-700': editor.isActive('bold') }"
        class="p-2 rounded hover:bg-gray-200 transition-colors"
        title="굵게"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path></svg>
      </button>

      <!-- Italic -->
      <button 
        type="button"
        @click="editor.chain().focus().toggleItalic().run()" 
        :class="{ 'bg-indigo-100 text-indigo-700': editor.isActive('italic') }"
        class="p-2 rounded hover:bg-gray-200 transition-colors"
        title="기울임"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="4" x2="10" y2="4"></line><line x1="14" y1="20" x2="5" y2="20"></line><line x1="15" y1="4" x2="9" y2="20"></line></svg>
      </button>

      <!-- Underline -->
      <button 
        type="button"
        @click="editor.chain().focus().toggleUnderline().run()" 
        :class="{ 'bg-indigo-100 text-indigo-700': editor.isActive('underline') }"
        class="p-2 rounded hover:bg-gray-200 transition-colors"
        title="밑줄"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"></path><line x1="4" y1="21" x2="20" y2="21"></line></svg>
      </button>

      <div class="w-px h-6 bg-gray-300 mx-1 align-middle self-center"></div>

      <!-- Bullet List -->
      <button 
        type="button"
        @click="editor.chain().focus().toggleBulletList().run()" 
        :class="{ 'bg-indigo-100 text-indigo-700': editor.isActive('bulletList') }"
        class="p-2 rounded hover:bg-gray-200 transition-colors"
        title="글머리 기호"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
      </button>

      <!-- Ordered List -->
      <button 
        type="button"
        @click="editor.chain().focus().toggleOrderedList().run()" 
        :class="{ 'bg-indigo-100 text-indigo-700': editor.isActive('orderedList') }"
        class="p-2 rounded hover:bg-gray-200 transition-colors"
        title="번호 매기기"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="10" y1="6" x2="21" y2="6"></line><line x1="10" y1="12" x2="21" y2="12"></line><line x1="10" y1="18" x2="21" y2="18"></line><path d="M4 6h1v4"></path><path d="M4 10h2"></path><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"></path></svg>
      </button>

      <div class="w-px h-6 bg-gray-300 mx-1 align-middle self-center"></div>

      <!-- Link -->
      <button 
        type="button"
        @click="setLink" 
        :class="{ 'bg-indigo-100 text-indigo-700': editor.isActive('link') }"
        class="p-2 rounded hover:bg-gray-200 transition-colors"
        title="링크 삽입"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
      </button>

      <!-- Image -->
      <button 
        type="button"
        @click="triggerImageUpload" 
        class="p-2 rounded hover:bg-gray-200 transition-colors text-gray-700"
        title="이미지 업로드"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
      </button>
      <input 
        type="file" 
        ref="imageInput" 
        class="hidden" 
        accept="image/*" 
        @change="handleImageUpload"
      >

      <div class="w-px h-6 bg-gray-300 mx-1 align-middle self-center"></div>

      <!-- Clear Formatting -->
      <button 
        type="button"
        @click="editor.chain().focus().unsetAllMarks().run()" 
        class="p-2 rounded hover:bg-gray-200 transition-colors text-gray-500"
        title="서식 지우기"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"></path></svg>
      </button>
    </div>

    <!-- Editor Content -->
    <editor-content 
        :editor="editor" 
        class="min-h-[300px] max-h-[600px] overflow-y-auto p-4 prose prose-indigo max-w-none focus:outline-none" 
    />
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import { ref, onBeforeUnmount, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const imageInput = ref(null);

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Underline,
    Link.configure({
      openOnClick: false,
      HTMLAttributes: {
        class: 'text-indigo-600 underline cursor-pointer',
      },
    }),
    Image.configure({
      HTMLAttributes: {
        class: 'rounded-xl shadow-lg max-w-full my-4',
      },
    }),
  ],
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML());
  },
  editorProps: {
    attributes: {
      class: 'focus:outline-none',
    },
  },
});

// Sync external value changes (for initial load or reset)
watch(() => props.modelValue, (value) => {
  const isSame = editor.value.getHTML() === value;
  if (isSame) return;
  editor.value.commands.setContent(value, false);
});

onBeforeUnmount(() => {
  editor.value.destroy();
});

const setLink = () => {
  const previousUrl = editor.value.getAttributes('link').href;
  const url = window.prompt('URL을 입력하세요:', previousUrl);

  if (url === null) return;
  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
    return;
  }

  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

const triggerImageUpload = () => {
  imageInput.value.click();
};

const handleImageUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);

  try {
    const response = await axios.post('/api/upload-image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    if (response.data.url) {
      editor.value.chain().focus().setImage({ src: response.data.url }).run();
    }
  } catch (error) {
    console.error('이미지 업로드 실패:', error);
    const message = error.response?.data?.error || '이미지 업로드에 실패했습니다. (최대 5MB)';
    alert(message);
  } finally {
    event.target.value = ''; // Reset input
  }
};
</script>

<style>
/* Tiptap Placeholder or basic styles if needed */
.ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #adb5bd;
  pointer-events: none;
  height: 0;
}
.ProseMirror {
    min-height: 300px;
}
</style>
