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
        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-50 text-gray-500 border border-gray-100 uppercase tracking-wider">
          #{{ tag }}
        </span>
      </div>

      <!-- Title -->
      <h2 class="text-lg font-bold text-gray-900 mb-4 leading-snug hover:text-primary cursor-pointer transition-colors line-clamp-2">
        {{ title }}
      </h2>

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
        <button class="flex items-center space-x-1.5 hover:text-red-500 transition-colors group">
          <svg class="w-5 h-5 group-hover:fill-red-50 group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
          <span class="text-xs font-bold">{{ likes }}</span>
        </button>
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
defineProps({
  authorName: { type: String, required: true },
  authorAvatar: { type: String, required: true },
  timeAgo: { type: String, required: true },
  category: { type: String, required: true },
  tag: { type: String, required: true },
  title: { type: String, required: true },
  summary: { type: Array, required: true },
  likes: { type: [Number, String], default: 0 },
});
</script>
