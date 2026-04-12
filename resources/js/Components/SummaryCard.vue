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
              <svg v-if="type !== 'notice'" class="w-4 h-4 ml-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
              <span v-else class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 uppercase tracking-tighter">운영공지</span>
            </p>
            <p class="text-xs font-medium text-gray-500">{{ timeAgo }} · {{ category }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-2">
            <span v-if="isPinned" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase tracking-wider">
                📌 고정공지
            </span>
            <div class="flex space-x-1" v-if="tags && tags.length > 0">
              <span v-for="t in tags" :key="t" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-50 text-gray-500 border border-gray-100 uppercase tracking-wider">
                #{{ t }}
              </span>
            </div>
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
    
    <div class="px-5 py-3.5 bg-white border-t border-gray-50 flex items-center justify-between text-gray-400">
      <div class="flex space-x-5">
        <!-- Likes -->
        <button class="flex items-center space-x-1.5 hover:text-indigo-500 transition-colors group">
          <svg class="w-[18px] h-[18px] group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
          </svg>
          <span class="text-xs font-extrabold">{{ likes }}</span>
        </button>

        <!-- Comments -->
        <button class="flex items-center space-x-1.5 hover:text-indigo-500 transition-colors group">
          <svg class="w-[18px] h-[18px] group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
          </svg>
          <span class="text-xs font-extrabold">{{ comments }}</span>
        </button>

        <!-- Views -->
        <div class="flex items-center space-x-1.5 hover:text-indigo-500 transition-colors group cursor-default">
          <svg class="w-[19px] h-[19px] group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
          <span class="text-xs font-extrabold">{{ views }}</span>
        </div>

        <!-- Share -->
        <button class="flex items-center space-x-1.5 hover:text-indigo-500 transition-colors group">
          <svg class="w-[18px] h-[18px] group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="18" cy="5" r="3"></circle>
            <circle cx="6" cy="12" r="3"></circle>
            <circle cx="18" cy="19" r="3"></circle>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
          </svg>
          <span class="text-[11px] font-bold uppercase tracking-wider">공유</span>
        </button>
      </div>

      <!-- Bookmark -->
      <button class="hover:text-amber-500 transition-colors hover:scale-110 group">
        <svg class="w-5 h-5 group-hover:fill-amber-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
        </svg>
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
  comments: { type: [Number, String], default: 0 },
  views: { type: [Number, String], default: 0 },
  type: { type: String, default: 'general' },
  isPinned: { type: Boolean, default: false },
});
</script>
