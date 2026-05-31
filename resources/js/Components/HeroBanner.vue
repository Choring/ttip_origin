<template>
  <Link
    :href="heroEvent ? route('events.show', heroEvent.event_seq) : route('events.index')"
    class="block relative overflow-hidden group"
    style="height: 400px;"
  >
    <!-- 배경 이미지 -->
    <img
      v-if="heroEvent?.image"
      :src="heroEvent.image"
      alt="오늘의 대구"
      class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
      @error="onImageError"
    />
    <img
      v-else
      src="/images/banner/tour-banner.jpg"
      alt="오늘의 대구"
      class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
    />

    <!-- 그라디언트 오버레이 -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>

    <!-- 콘텐츠 -->
    <div class="absolute inset-0 flex flex-col justify-between p-5">
      <!-- 상단: 날짜 -->
      <div class="flex items-center justify-between">
        <span class="text-white/70 text-xs font-semibold">{{ todayStr }}</span>
        <span class="bg-orange-500 text-white text-[11px] font-black px-2.5 py-1 rounded-full">
          오늘의 대구
        </span>
      </div>

      <!-- 하단: 행사 정보 -->
      <div>
        <p class="text-white/70 text-xs font-semibold mb-1 flex items-center gap-1">
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          {{ heroEvent?.place ?? '대구광역시' }}
        </p>
        <h2 class="text-white font-black text-lg leading-snug line-clamp-2 drop-shadow mb-2">
          {{ heroEvent?.subject ?? '대구의 다양한 공연과 행사를 만나보세요' }}
        </h2>
        <div class="flex items-center gap-2">
          <span v-if="heroEvent" class="text-white/60 text-xs">
            📅 {{ heroEvent.start_date }} ~ {{ heroEvent.end_date }}
          </span>
          <span class="text-orange-300 text-xs font-bold group-hover:text-orange-200 transition-colors ml-auto">
            자세히 보기 →
          </span>
        </div>
      </div>
    </div>
  </Link>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  heroEvent: { type: Object, default: null },
});

const todayStr = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('ko-KR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    weekday: 'short',
  });
});

const onImageError = (e) => {
  e.target.src = '/images/banner/tour-banner.jpg';
};
</script>
