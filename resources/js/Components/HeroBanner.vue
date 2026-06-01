<template>
  <!-- DB 배너 (첫 번째) -->
  <component
    :is="currentBanner.link_url ? Link : 'div'"
    :href="currentBanner.link_url || undefined"
    class="block relative overflow-hidden group"
    style="height: 400px;"
  >
    <!-- 배경 이미지 -->
    <img
      v-if="currentBanner.image"
      :src="currentBanner.image"
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
    <div class="absolute inset-0 flex flex-col justify-between p-6 max-w-7xl mx-auto">
      <!-- 상단: 날짜 + 배지 -->
      <div class="flex items-center justify-between">
        <span class="text-white/70 text-sm font-semibold">{{ todayStr }}</span>
        <span class="bg-orange-500 text-white text-xs font-black px-3 py-1.5 rounded-full">
          오늘의 대구
        </span>
      </div>

      <!-- 하단: 텍스트 -->
      <div>
        <p v-if="currentBanner.subtitle" class="text-white/70 text-sm font-semibold mb-2 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          {{ currentBanner.subtitle }}
        </p>
        <h2 class="text-white font-black text-2xl leading-snug drop-shadow mb-3">
          {{ currentBanner.title }}
        </h2>
        <span v-if="currentBanner.link_url" class="text-orange-300 text-sm font-bold group-hover:text-orange-200 transition-colors">
          자세히 보기 →
        </span>
      </div>
    </div>
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  banners:   { type: Array,  default: () => [] },
  heroEvent: { type: Object, default: null },
});

// DB 배너가 있으면 첫 번째 배너 사용, 없으면 KOPIS fallback
const currentBanner = computed(() => {
  if (props.banners && props.banners.length > 0) {
    const b = props.banners[0];
    return {
      title:      b.title,
      subtitle:   b.subtitle,
      image:      b.image_url,
      link_url:   b.link_url,
    };
  }

  // KOPIS fallback
  if (props.heroEvent) {
    return {
      title:      props.heroEvent.subject,
      subtitle:   props.heroEvent.place,
      image:      props.heroEvent.image,
      link_url:   route('events.show', props.heroEvent.event_seq),
    };
  }

  // 기본값
  return {
    title:      '대구의 다양한 공연과 행사를 만나보세요',
    subtitle:   '대구광역시',
    badge_text: '오늘의 대구',
    image:      null,
    link_url:   route('events.index'),
  };
});

const todayStr = computed(() => new Date().toLocaleDateString('ko-KR', {
  year: 'numeric', month: 'long', day: 'numeric', weekday: 'short',
}));

const onImageError = (e) => {
  e.target.src = '/images/banner/tour-banner.jpg';
};
</script>
