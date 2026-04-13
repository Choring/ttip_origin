<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 hover:border-green-200 hover:shadow-md transition-all duration-200 group relative overflow-hidden">
    <Link :href="route('posts.show', id)" class="flex items-center gap-4">
      <!-- Left: Category / Wage Info -->
      <div class="flex flex-col items-center justify-center min-w-[70px] py-1 bg-green-50 rounded-lg border border-green-100">
        <span class="text-[9px] font-black text-green-600 uppercase tracking-tighter mb-0.5">시급</span>
        <span class="text-sm font-black text-green-700 tracking-tight">{{ extra_info?.wage || '-' }}</span>
      </div>

      <!-- Center: Job Info -->
      <div class="flex-1 min-w-0">
        <img 
          v-if="card_image_url" 
          :src="card_image_url" 
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
          alt="Post image"
        >
        <h2 class="text-[14px] font-bold text-gray-900 leading-tight line-clamp-1 mb-1 group-hover:text-green-600 transition-colors">
          {{ title }}
        </h2>
        <div class="flex items-center gap-2 flex-wrap">
          <span v-if="extra_info?.industry" class="text-[10px] bg-gray-50 text-gray-500 px-1.5 py-0.5 rounded border border-gray-100">
            {{ extra_info.industry }}
          </span>
          <span v-if="extra_info?.location" class="text-[10px] text-gray-400 font-medium">
            📍 {{ extra_info.location }}
          </span>
          <span v-if="extra_info?.hours" class="text-[10px] text-gray-400 font-medium">
            ⏰ {{ extra_info.hours }}
          </span>
        </div>
      </div>

      <!-- Right: Meta (Always on the right) -->
      <div class="flex flex-col items-end gap-1.5 ml-auto">
        <span class="text-[10px] text-gray-300 font-medium whitespace-nowrap">{{ timeAgo }}</span>
        <div class="flex items-center gap-1">
           <img :src="authorAvatar" class="w-3.5 h-3.5 rounded-full" alt="">
           <span class="text-[9px] text-gray-400 font-medium whitespace-nowrap">{{ authorName }}</span>
        </div>
      </div>
    </Link>

    <!-- Ad Highlight (Subtle) -->
    <div v-if="type === 'ad'" class="absolute top-0 right-0">
        <div class="bg-red-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-bl-lg uppercase">AD</div>
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
  title: { type: String, required: true },
  type: { type: String, default: 'general' },
  card_image_url: { type: String, default: null },
  extra_info: { type: Object, default: () => ({}) },
});
</script>
