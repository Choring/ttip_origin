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
          loading="lazy"
          fetchpriority="low"
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
    </Link>

    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50/50">
      <div class="flex items-center gap-1.5 min-w-0">
        <img :src="authorAvatar" class="w-3.5 h-3.5 rounded-full" alt="">
        <span class="text-[9px] text-gray-400 font-medium whitespace-nowrap overflow-hidden truncate max-w-[60px]">{{ authorName }}</span>
      </div>
      <!-- 공유 및 시간 -->
      <div class="flex items-center gap-2">
        <span class="text-[9px] text-gray-300 font-medium whitespace-nowrap">{{ timeAgo }}</span>
        <button 
          @click.prevent="sharePost"
          class="text-gray-300 hover:text-green-500 transition-colors"
          title="공유하기"
        >
          <svg class="w-3.5 h-3.5 font-bold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
        </button>
      </div>
    </div>


    <!-- Bookmark Button (Absolute) -->
    <button 
      v-if="$page.props.auth.user"
      @click.prevent="toggleBookmark" 
      class="absolute top-2.5 right-2.5 p-1.5 rounded-lg transition-all"
      :class="isBookmarked ? 'bg-amber-100 text-amber-500' : 'text-gray-200 hover:bg-gray-50 hover:text-amber-400'"
    >
      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" :fill="isBookmarked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="3"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
    </button>


    <!-- Ad Highlight (Subtle) -->
    <div v-if="type === 'ad'" class="absolute top-0 right-0">
        <div class="bg-red-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-bl-lg uppercase">AD</div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

const { showToast } = useToast();


const props = defineProps({
  id: { type: [Number, String], required: true },
  authorName: { type: String, required: true },
  authorAvatar: { type: String, required: true },
  timeAgo: { type: String, required: true },
  category: { type: String, required: true },
  title: { type: String, required: true },
  type: { type: String, default: 'general' },
  card_image_url: { type: String, default: null },
  extra_info: { type: Object, default: () => ({}) },
  isBookmarked: { type: Boolean, default: false },
});

const toggleBookmark = () => {
    router.post(route('posts.bookmark', props.id), {}, {
        preserveScroll: true,
        onError: () => {
            showToast('북마크 처리에 실패했습니다. 다시 시도해 주세요. ⚠️', 'error');
        }
    });
};

const sharePost = async () => {
    const shareData = {
        title: props.title,
        url: route('posts.show', props.id),
    };

    try {
        if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
            await navigator.share(shareData);
        } else {
            throw new Error('Web Share not supported');
        }
    } catch (err) {
        try {
            await navigator.clipboard.writeText(shareData.url);
            showToast('공유 링크가 클립보드에 복사되었습니다! ✅');
        } catch (copyErr) {
            showToast('링크 복사에 실패했습니다. 직접 주소를 복사해 주세요. ⚠️', 'error');
        }
    }
};
</script>


