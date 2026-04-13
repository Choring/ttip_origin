<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300 group">
    <!-- Image Section -->
    <Link :href="route('posts.show', id)" class="block relative aspect-[4/3] overflow-hidden">
      <img 
        v-if="card_image_url" 
        :src="card_image_url" 
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
        alt="Post image"
      >
      <div v-else class="w-full h-full bg-indigo-50 flex items-center justify-center">
        <span class="text-4xl text-indigo-200">{{ categoryIcon }}</span>
      </div>
      
      <!-- Overlaid Badges -->
      <div class="absolute top-3 left-3 flex flex-col gap-1.5">
        <span v-if="extra_info?.price" class="bg-black/60 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[10px] font-bold">
          {{ extra_info.price }}
        </span>
        <span v-if="extra_info?.waiting" class="bg-indigo-600/80 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[10px] font-bold">
          ⏱️ {{ extra_info.waiting }}
        </span>
      </div>
    </Link>
    
    <!-- Bookmark Button Overlay -->
    <button 
      v-if="$page.props.auth.user"
      @click.prevent="toggleBookmark" 
      class="absolute top-3 right-3 p-1.5 rounded-full backdrop-blur-md transition-all shadow-sm z-10"
      :class="isBookmarked ? 'bg-amber-500 text-white' : 'bg-white/60 text-gray-400 hover:bg-white hover:text-amber-500'"
    >
      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" :fill="isBookmarked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="3"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
    </button>


    <!-- Info Section -->
    <div class="p-4">
      <div class="flex items-center gap-2 mb-2">
        <span class="text-[10px] font-black px-1.5 py-0.5 rounded" :class="badgeClass">
          {{ category }}
        </span>
        <span v-if="extra_info?.location" class="text-[10px] text-gray-400 font-medium truncate max-w-[120px]">
          📍 {{ extra_info.location }}
        </span>
      </div>

      <Link :href="route('posts.show', id)">
        <h2 class="text-sm font-bold text-gray-900 leading-snug line-clamp-1 group-hover:text-indigo-600 transition-colors mb-2">
          {{ title }}
        </h2>
      </Link>

      <div class="flex items-center justify-between mt-auto">
        <div class="flex items-center gap-1.5">
          <img :src="authorAvatar" class="w-4 h-4 rounded-full" alt="">
          <span class="text-[10px] text-gray-400 font-medium">{{ authorName }}</span>
        </div>
        <div class="flex items-center gap-2 text-gray-300">
           <span class="flex items-center gap-1 text-[10px] font-bold">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            {{ comments }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useToast } from '@/Composables/useToast';

const { showToast } = useToast();


const props = defineProps({
  id: { type: [Number, String], required: true },
  authorName: { type: String, required: true },
  authorAvatar: { type: String, required: true },
  category: { type: String, required: true },
  categorySlug: { type: String, required: true },
  title: { type: String, required: true },
  comments: { type: [Number, String], default: 0 },
  card_image_url: { type: String, default: null },
  extra_info: { type: Object, default: () => ({}) },
  isBookmarked: { type: Boolean, default: false },
});


const categoryIcon = computed(() => {
  const icons = {
    'restaurant': '🍱',
    'cafe': '☕',
    'solo-dining': '🍜',
  };
  return icons[props.categorySlug] || '📍';
});

const badgeClass = computed(() => {
  const classes = {
    'restaurant': 'bg-orange-100 text-orange-600',
    'cafe': 'bg-amber-100 text-amber-700',
    'solo-dining': 'bg-yellow-100 text-yellow-700',
  };
  return classes[props.categorySlug] || 'bg-gray-100 text-gray-600';
});

const toggleBookmark = () => {
    router.post(route('posts.bookmark', props.id), {}, {
        preserveScroll: true,
        onError: () => {
            showToast('북마크 처리에 실패했습니다. 다시 시도해 주세요. ⚠️', 'error');
        }
    });
};
</script>

