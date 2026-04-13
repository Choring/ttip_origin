<template>
  <!-- 고정공지 -->
  <div
    v-if="type === 'notice'"
    class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center gap-3 hover:bg-amber-100 transition-colors"
  >
    <span class="text-base flex-shrink-0">📌</span>
    <Link :href="route('posts.show', id)" class="flex-1 min-w-0">
      <span class="text-sm font-bold text-amber-800 line-clamp-1">{{ title }}</span>
    </Link>
    <span class="text-xs text-amber-500 flex-shrink-0 whitespace-nowrap">{{ timeAgo }}</span>
  </div>

  <!-- 광고 카드 -->
  <div
    v-else-if="type === 'ad'"
    class="bg-white border border-gray-100 rounded-2xl p-4 flex gap-4 items-start hover:shadow-md transition-shadow duration-200 opacity-80"
  >
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 mb-1.5">
        <span class="text-[10px] font-black bg-red-500 text-white px-1.5 py-0.5 rounded tracking-tight">광고</span>
        <span class="text-xs text-gray-400">{{ category }}</span>
      </div>
      <Link :href="route('posts.show', id)">
        <h2 class="text-base font-bold text-gray-700 leading-snug line-clamp-2 hover:text-gray-900 transition-colors mb-2">{{ title }}</h2>
      </Link>
      <p v-if="summary && summary.length > 0" class="text-xs text-gray-400 line-clamp-1">{{ summary[0] }}</p>
    </div>
  </div>

  <!-- 일반 게시글 카드 -->
  <div
    v-else
    class="bg-white border border-gray-100 rounded-2xl hover:shadow-md transition-all duration-200 overflow-hidden group"
  >
    <Link :href="route('posts.show', id)" class="block p-4 sm:p-5">
      <!-- 상단: 카테고리 + 태그 + 시간 -->
      <div class="flex items-center justify-between mb-2.5">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="text-[11px] font-bold px-2 py-0.5 rounded-full" :class="categoryStyle.badge">
            {{ category }}
          </span>
          <span
            v-for="t in (tags || []).slice(0, 2)"
            :key="t"
            class="text-[11px] text-gray-400 font-medium"
          >#{{ t }}</span>
        </div>
        <span class="text-[11px] text-gray-300 flex-shrink-0 ml-2">{{ timeAgo }}</span>
      </div>

      <!-- 제목 -->
      <h2 class="text-[15px] font-bold text-gray-900 leading-snug line-clamp-2 mb-2 group-hover:text-indigo-600 transition-colors">
        {{ title }}
      </h2>

      <!-- 한줄 요약 (있을 때만) -->
      <p v-if="summary && summary.length > 0" class="text-xs text-gray-400 line-clamp-1 mb-3">
        {{ summary[0] }}
      </p>

      <!-- 하단: 작성자 + 통계 -->
      <div class="flex items-center justify-between pt-3 border-t border-gray-50">
        <!-- 작성자 -->
        <div class="flex items-center gap-2">
          <img :src="authorAvatar" class="w-5 h-5 rounded-full object-cover" alt="">
          <span class="text-xs text-gray-400 font-medium">{{ authorName }}</span>
        </div>

        <!-- 통계 -->
        <div class="flex items-center gap-3 text-gray-400">
          <!-- 좋아요 -->
          <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
            <span class="text-[11px] font-bold">{{ likes }}</span>
          </span>
          <!-- 댓글 -->
          <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span class="text-[11px] font-bold">{{ comments }}</span>
          </span>
          <!-- 조회수 -->
          <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <span class="text-[11px] font-bold">{{ views }}</span>
          </span>
        </div>
      </div>
    </Link>

    <!-- 액션 버튼 (공유 / 북마크) -->
    <div class="px-4 sm:px-5 pb-3 flex justify-end gap-3 -mt-1">
      <button @click.prevent="sharePost" class="text-gray-300 hover:text-indigo-400 transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
      </button>
      <button class="text-gray-300 hover:text-amber-400 transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useToast } from '@/Composables/useToast';

const { showToast } = useToast();

const props = defineProps({
  id: { type: [Number, String], required: true },
  authorName: { type: String, required: true },
  authorAvatar: { type: String, required: true },
  timeAgo: { type: String, required: true },
  category: { type: String, required: true },
  tags: { type: Array, default: () => [] },
  title: { type: String, required: true },
  summary: { type: Array, default: () => [] },
  likes: { type: [Number, String], default: 0 },
  comments: { type: [Number, String], default: 0 },
  views: { type: [Number, String], default: 0 },
  type: { type: String, default: 'general' },
  isPinned: { type: Boolean, default: false },
  categorySlug: { type: String, default: 'general' },
});

const categoryStyle = computed(() => {
  const map = {
    'restaurant':  { badge: 'bg-orange-100 text-orange-600' },
    'cafe':        { badge: 'bg-amber-100 text-amber-700' },
    'solo-dining': { badge: 'bg-yellow-100 text-yellow-700' },
    'gym':         { badge: 'bg-blue-100 text-blue-600' },
    'part-time':   { badge: 'bg-green-100 text-green-700' },
    'notice':      { badge: 'bg-amber-100 text-amber-700' },
  };
  return map[props.categorySlug] ?? { badge: 'bg-indigo-100 text-indigo-600' };
});

const sharePost = async () => {
    const shareData = {
        title: props.title,
        text: props.summary.join(' '),
        url: route('posts.show', props.id),
    };

    try {
        if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
            await navigator.share(shareData);
        } else {
            throw new Error('Web Share not supported');
        }
    } catch (err) {
        // Fallback to Clipboard
        try {
            await navigator.clipboard.writeText(shareData.url);
            showToast('공유 링크가 클립보드에 복사되었습니다! ✅');
        } catch (copyErr) {
            showToast('링크 복사에 실패했습니다. 직접 주소를 복사해 주세요. ⚠️', 'error');
        }
    }
};
</script>
