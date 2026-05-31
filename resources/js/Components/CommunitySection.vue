<template>
  <section v-if="posts && posts.length > 0">
    <!-- 섹션 헤더 -->
    <div class="flex items-center justify-between mb-3 px-1">
      <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
        💬 <span>커뮤니티 최신글</span>
      </h2>
      <Link :href="route('community')" class="text-xs font-bold text-gray-400 hover:text-orange-500 transition-colors">
        더보기 →
      </Link>
    </div>

    <!-- 게시글 리스트 -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden divide-y divide-gray-50">
      <Link
        v-for="post in posts.slice(0, 5)"
        :key="post.id"
        :href="route('posts.show', post.id)"
        class="flex items-start gap-3 px-4 py-3.5 hover:bg-gray-50 transition-colors group"
      >
        <!-- 카테고리 배지 -->
        <span class="flex-shrink-0 mt-0.5 text-[10px] font-black px-2 py-0.5 rounded-full whitespace-nowrap"
          :class="categoryStyle(post.categorySlug)"
        >
          {{ post.category }}
        </span>

        <!-- 제목 + 메타 -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-gray-900 line-clamp-1 group-hover:text-orange-500 transition-colors leading-snug mb-1">
            {{ post.title }}
          </p>
          <div class="flex items-center gap-2 text-[11px] text-gray-400">
            <span>{{ post.authorName }}</span>
            <span>·</span>
            <span>{{ post.timeAgo }}</span>
          </div>
        </div>

        <!-- 통계 -->
        <div class="flex-shrink-0 flex items-center gap-2 text-[11px] text-gray-400">
          <span class="flex items-center gap-0.5">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
            </svg>
            {{ post.likes }}
          </span>
          <span class="flex items-center gap-0.5">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            {{ post.comments }}
          </span>
        </div>
      </Link>
    </div>

    <!-- 더보기 버튼 -->
    <div class="mt-3 text-center">
      <Link
        :href="route('community')"
        class="inline-flex items-center gap-1.5 px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-full hover:border-orange-300 hover:text-orange-500 transition-all shadow-sm"
      >
        커뮤니티 글 더보기 →
      </Link>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  posts: { type: Array, default: () => [] },
});

const categoryStyle = (slug) => {
  const map = {
    'restaurant':  'bg-orange-100 text-orange-600',
    'cafe':        'bg-amber-100 text-amber-700',
    'solo-dining': 'bg-yellow-100 text-yellow-700',
    'gym':         'bg-blue-100 text-blue-600',
    'part-time':   'bg-green-100 text-green-700',
  };
  return map[slug] ?? 'bg-indigo-100 text-indigo-600';
};
</script>
