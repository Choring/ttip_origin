<template>
  <div class="relative overflow-hidden rounded-xl" style="height: 400px;">

    <!-- 슬라이드 목록 -->
    <div
      class="flex h-full transition-transform duration-500 ease-in-out"
      :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
    >
      <component
        v-for="(slide, index) in slides"
        :key="index"
        :is="slide.link_url ? Link : 'div'"
        :href="slide.link_url || undefined"
        class="flex-shrink-0 w-full h-full relative group block"
      >
        <!-- 배경 이미지 -->
        <img
          :src="slide.image"
          :alt="slide.title"
          class="absolute inset-0 w-full h-full object-cover"
          @error="e => e.target.src = '/images/banner/tour-banner.jpg'"
        />
        <!-- 그라디언트 오버레이 -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

        <!-- 텍스트 -->
        <div class="absolute inset-0 flex flex-col justify-end p-7">
          <!-- 장소 pill -->
          <div v-if="slide.subtitle" class="flex items-center gap-1.5 mb-3">
            <span class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full border border-white/30">
              <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ slide.subtitle }}
            </span>
          </div>

          <!-- 제목 -->
          <h2 class="text-white font-black text-3xl leading-tight drop-shadow-lg mb-4">
            {{ slide.title }}
          </h2>

          <!-- CTA 버튼 -->
          <div v-if="slide.link_url" class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-400 text-white text-sm font-black px-5 py-2.5 rounded-full transition-colors shadow-lg">
              자세히 보기
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="9 18 15 12 9 6"/>
              </svg>
            </span>
          </div>
        </div>

        <!-- 슬라이드 카운터 (우측 하단) -->
        <div v-if="slides.length > 1" class="absolute bottom-7 right-7 bg-black/40 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full">
          {{ currentIndex + 1 }} / {{ slides.length }}
        </div>
      </component>
    </div>

    <!-- 이전/다음 버튼 (슬라이드 2개 이상일 때만) -->
    <template v-if="slides.length > 1">
      <button
        @click="prev"
        class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/30 hover:bg-black/50 text-white rounded-full flex items-center justify-center transition-colors backdrop-blur-sm z-10"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
      </button>
      <button
        @click="next"
        class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/30 hover:bg-black/50 text-white rounded-full flex items-center justify-center transition-colors backdrop-blur-sm z-10"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </button>

      <!-- 하단 인디케이터 -->
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
        <button
          v-for="(_, i) in slides"
          :key="i"
          @click="goTo(i)"
          class="h-1.5 rounded-full transition-all duration-300"
          :class="i === currentIndex ? 'bg-white w-6' : 'bg-white/50 w-1.5'"
        />
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted  } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  banners:   { type: Array,  default: () => [] },
  heroEvent: { type: Object, default: null },
});

// DB 배너 → KOPIS fallback → 기본값
const slides = computed(() => {
  if (props.banners && props.banners.length > 0) {
    return props.banners.map(b => ({
      title:    b.title,
      subtitle: b.subtitle,
      image:    b.image_url,
      link_url: b.link_url,
    }));
  }
  if (props.heroEvent) {
    return [{
      title:    props.heroEvent.subject,
      subtitle: props.heroEvent.place,
      image:    props.heroEvent.image,
      link_url: route('events.show', props.heroEvent.event_seq),
    }];
  }
  return [{
    title:    '대구의 다양한 공연과 행사를 만나보세요',
    subtitle: '대구광역시',
    image:    null,
    link_url: route('events.index'),
  }];
});

const currentIndex = ref(0);
let timer = null;

const next = () => { currentIndex.value = (currentIndex.value + 1) % slides.value.length; resetTimer(); };
const prev = () => { currentIndex.value = (currentIndex.value - 1 + slides.value.length) % slides.value.length; resetTimer(); };
const goTo = (i) => { currentIndex.value = i; resetTimer(); };

const startTimer = () => {
  if (slides.value.length <= 1) return;
  timer = setInterval(next, 5000);
};
const resetTimer = () => { clearInterval(timer); startTimer(); };

onMounted(startTimer);
onUnmounted(() => clearInterval(timer));

</script>
