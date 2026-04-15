<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PopularPostsWidget from '@/Components/PopularPostsWidget.vue';
import HallOfFameWidget from '@/Components/HallOfFameWidget.vue';
import PartnerSitesWidget from '@/Components/PartnerSitesWidget.vue';

const page = usePage();
const hasHallOfFame = computed(() => {
    const rankings = page.props.hall_of_fame || page.props.hallOfFame || [];
    return rankings.length > 0;
});
</script>

<template>
  <div class="flex flex-col gap-6">
    <!-- 명예의 전당 (데이터 있을 때만) -->
    <HallOfFameWidget v-if="hasHallOfFame" />

    <!-- 실시간 인기 게시글 -->
    <PopularPostsWidget />

    <!-- 추천 사이트 -->
    <PartnerSitesWidget />

    <!-- 우측 광고 (실제 광고 있을 때만) -->
    <div v-if="$page.props.advertisement" class="hidden md:flex bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 h-80 flex-col items-center justify-center text-gray-400">
      <svg class="h-8 w-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
      </svg>
      <span class="text-xs font-bold tracking-wider text-gray-400">ADVERTISEMENT</span>
    </div>
  </div>
</template>
