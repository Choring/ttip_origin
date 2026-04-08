<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <div class="p-5">
      <!-- Header / Author -->
      <div class="flex justify-between items-start mb-4">
        <div class="flex items-center space-x-3">
          <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-50" :src="authorAvatar" alt="Author avatar">
          <div>
            <p class="text-sm font-bold text-gray-900 flex items-center">
              {{ authorName }}
              <svg class="w-4 h-4 ml-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            </p>
            <p class="text-xs font-medium text-gray-500">{{ timeAgo }} · {{ category }}</p>
          </div>
        </div>
        <div class="flex space-x-1" v-if="tags && tags.length > 0">
          <span v-for="t in tags" :key="t" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-50 text-gray-500 border border-gray-100 uppercase tracking-wider">
            #{{ t }}
          </span>
        </div>
      </div>

      <!-- Title -->
      <Link :href="route('posts.show', id)">
        <h2 class="text-lg font-bold text-gray-900 mb-4 leading-snug hover:text-primary cursor-pointer transition-colors line-clamp-2">
          {{ title }}
        </h2>
      </Link>

      <!-- 3-line summary -->
      <div class="bg-gray-50 rounded-xl p-4 space-y-3.5 border border-gray-100/80">
        <div class="flex items-start" v-for="(line, index) in summary" :key="index">
          <div class="flex-shrink-0 mt-0.5">
            <svg class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <p class="ml-2.5 text-sm text-gray-700 font-medium leading-relaxed">
            {{ line }}
          </p>
        </div>
      </div>
    </div>
    
    <!-- Footer Actions -->
    <div class="px-5 py-3.5 bg-white border-t border-gray-50 flex items-center justify-between text-gray-400">
      <div class="flex space-x-5">
        <div class="flex items-center space-x-1.5 text-indigo-500">
          <span class="text-base leading-none translate-y-[-1px]">👍</span>
          <span class="text-xs font-bold">{{ likes }}</span>
        </div>
        <div class="flex items-center space-x-1.5 hover:text-gray-600 transition-colors">
          <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
          <span class="text-xs font-bold">{{ views }}</span>
        </div>
        <button class="flex items-center space-x-1.5 hover:text-primary transition-colors group">
          <svg class="w-5 h-5 group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
          <span class="text-xs font-bold">공유</span>
        </button>
      </div>
      <button class="hover:text-primary transition-colors hover:scale-110">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  id: { type: [Number, String], required: true },
  authorName: { type: String, required: true },
  authorAvatar: { type: String, required: true },
  timeAgo: { type: String, required: true },
  category: { type: String, required: true },
  tags: { type: Array, default: () => [] },
  title: { type: String, required: true },
  summary: { type: Array, required: true },
  likes: { type: [Number, String], default: 0 },
  views: { type: [Number, String], default: 0 },
});
</script>
