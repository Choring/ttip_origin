<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-300 group flex gap-5">
    <!-- Left: Thumbnail (fixed size) -->
    <Link :href="route('posts.show', id)" class="w-24 sm:w-32 aspect-square flex-shrink-0 bg-blue-50 rounded-xl overflow-hidden relative">
      <img 
        v-if="card_image_path" 
        :src="'/storage/' + card_image_path" 
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
        alt="Gym image"
      >
      <div v-else class="w-full h-full flex items-center justify-center text-3xl opacity-30">🏋️</div>
    </Link>

    <!-- Right: Info -->
    <div class="flex-1 flex flex-col justify-between py-0.5">
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-[10px] font-black bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded uppercase">GYM</span>
          <span v-if="extra_info?.fee" class="text-sm font-black text-blue-600 tracking-tight">{{ extra_info.fee }}</span>
        </div>
        <Link :href="route('posts.show', id)">
          <h2 class="text-base font-bold text-gray-900 line-clamp-1 group-hover:text-blue-600 transition-colors mb-1">{{ title }}</h2>
        </Link>
        <p v-if="extra_info?.location" class="text-xs text-gray-400 font-medium mb-2">📍 {{ extra_info.location }}</p>
        
        <div class="flex flex-wrap gap-2 mt-2">
            <span v-if="extra_info?.hours" class="text-[10px] bg-gray-50 text-gray-400 px-2 py-0.5 rounded-full border border-gray-100">
                ⏰ {{ extra_info.hours }}
            </span>
            <span v-if="extra_info?.facilities" class="text-[10px] bg-green-50 text-green-600 px-2 py-0.5 rounded-full border border-green-100">
                ⭐ {{ extra_info.facilities }}
            </span>
        </div>
      </div>

      <!-- Bottom: Author & Stats -->
      <div class="flex items-center justify-between pt-3 border-t border-gray-50">
        <div class="flex items-center gap-1.5">
          <img :src="authorAvatar" class="w-4 h-4 rounded-full" alt="">
          <span class="text-[10px] text-gray-400 font-medium">{{ authorName }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] text-gray-300 font-bold flex items-center gap-1">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                {{ views }}
            </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  id: { type: [Number, String], required: true },
  authorName: { type: String, required: true },
  authorAvatar: { type: String, required: true },
  title: { type: String, required: true },
  card_image_path: { type: String, default: null },
  extra_info: { type: Object, default: () => ({}) },
  views: { type: [Number, String], default: 0 },
});
</script>
