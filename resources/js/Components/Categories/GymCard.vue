<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-300 group flex gap-5">
    <!-- Left: Thumbnail (fixed size) -->
    <Link :href="route('posts.show', id)" class="w-24 sm:w-32 aspect-square flex-shrink-0 bg-blue-50 rounded-xl overflow-hidden relative">
      <img 
        v-if="card_image_url" 
        :src="card_image_url" 
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
        alt="Gym image"
        loading="lazy"
        fetchpriority="low"
      >
      <div v-else class="w-full h-full flex items-center justify-center text-3xl opacity-30">🏋️</div>
    </Link>

    <!-- Bookmark Button (Absolute) -->
    <button 
      v-if="$page.props.auth.user"
      @click.prevent="toggleBookmark" 
      class="absolute top-4 right-4 p-2 rounded-xl transition-all shadow-sm"
      :class="isBookmarked ? 'bg-amber-500 text-white' : 'bg-white border border-gray-100 text-gray-300 hover:text-amber-400'"
    >
      <svg class="w-4 h-4" viewBox="0 0 24 24" :fill="isBookmarked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
    </button>


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
            <!-- 공유 버튼 -->
            <button 
              @click.prevent="sharePost"
              class="text-gray-300 hover:text-blue-500 transition-colors ml-1"
              title="공유하기"
            >
              <svg class="w-3 h-3 font-bold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
            </button>
        </div>
      </div>
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
  title: { type: String, required: true },
  card_image_url: { type: String, default: null },
  extra_info: { type: Object, default: () => ({}) },
  views: { type: [Number, String], default: 0 },
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


