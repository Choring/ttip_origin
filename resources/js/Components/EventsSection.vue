<template>
  <section v-if="events && events.length > 0">
    <!-- 섹션 헤더 -->
    <div class="flex items-center justify-between mb-3 px-1">
      <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
        🎭 <span>이번 주 공연·행사</span>
      </h2>
      <Link :href="route('events.index')" class="text-xs font-bold text-gray-400 hover:text-orange-500 transition-colors">
        전체보기 →
      </Link>
    </div>

    <!-- 가로 스크롤 카드 -->
    <div class="relative -mx-4 px-4">
      <!-- 이전 버튼 -->
      <button
        @click="scrollLeft"
        class="hidden md:flex absolute left-0 top-1/2 -translate-y-8 z-10 w-9 h-9 bg-white border border-gray-200 rounded-full shadow-md items-center justify-center hover:bg-orange-50 hover:border-orange-300 transition-all"
      >
        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
      </button>
      <!-- 다음 버튼 -->
      <button
        @click="scrollRight"
        class="hidden md:flex absolute right-0 top-1/2 -translate-y-8 z-10 w-9 h-9 bg-white border border-gray-200 rounded-full shadow-md items-center justify-center hover:bg-orange-50 hover:border-orange-300 transition-all"
      >
        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </button>

      <div ref="scrollContainer" class="flex gap-3 overflow-x-auto pb-3 no-scrollbar scroll-smooth snap-x snap-mandatory">
        <Link
          v-for="event in events"
          :key="event.event_seq"
          :href="route('events.show', event.event_seq)"
          class="flex-shrink-0 w-52 snap-start group"
        >
          <!-- 이미지 -->
          <div class="relative w-full h-64 rounded-xl overflow-hidden mb-2 bg-gray-100">
            <img
              :src="event.image"
              :alt="event.subject"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
              @error="e => e.target.src = '/images/banner/tour-banner.jpg'"
            />
            <!-- 장르 배지 -->
            <span class="absolute top-2 left-2 text-[10px] font-black px-2 py-0.5 rounded-full backdrop-blur-sm"
              :class="genreStyle(event.event_gubun)"
            >
              {{ event.event_gubun || '공연' }}
            </span>
            <!-- 무료 배지 -->
            <span v-if="isFree(event.pay)" class="absolute top-2 right-2 text-[10px] font-black bg-green-500 text-white px-2 py-0.5 rounded-full">
              무료
            </span>
          </div>

          <!-- 텍스트 -->
          <p class="text-sm font-black text-gray-900 line-clamp-2 leading-snug mb-1 group-hover:text-orange-500 transition-colors">
            {{ event.subject }}
          </p>
          <p class="text-xs text-gray-500 line-clamp-1">📍 {{ event.place }}</p>
          <p class="text-xs text-gray-500 mt-0.5">{{ formatDate(event.start_date) }}</p>
        </Link>

        <!-- 더보기 카드 -->
        <Link
          :href="route('events.index')"
          class="flex-shrink-0 w-52 snap-start h-64 rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-2 hover:border-orange-300 hover:bg-orange-50 transition-all group"
        >
          <div class="w-10 h-10 rounded-full bg-gray-100 group-hover:bg-orange-100 flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-gray-400 group-hover:text-orange-500 transition-colors">더 보기</span>
        </Link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  events: { type: Array, default: () => [] },
});

const scrollContainer = ref(null);
const scrollLeft  = () => scrollContainer.value?.scrollBy({ left: -320, behavior: 'smooth' });
const scrollRight = () => scrollContainer.value?.scrollBy({ left:  320, behavior: 'smooth' });

const genreStyle = (gubun) => {
  const map = {
    '뮤지컬': 'bg-purple-500/90 text-white',
    '연극':   'bg-blue-500/90 text-white',
    '클래식': 'bg-indigo-500/90 text-white',
    '무용':   'bg-pink-500/90 text-white',
    '국악':   'bg-amber-500/90 text-white',
    '전시':   'bg-teal-500/90 text-white',
    '축제':   'bg-orange-500/90 text-white',
  };
  return map[gubun] ?? 'bg-gray-700/80 text-white';
};

const isFree = (pay) => {
  if (!pay) return false;
  return pay.includes('무료') || pay === '0' || pay === '0원';
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  // "2026.05.29" → "5.29 시작"
  const parts = dateStr.replace(/\./g, '-').split('-');
  if (parts.length >= 3) {
    return `${parseInt(parts[1])}.${parseInt(parts[2])} 시작`;
  }
  return dateStr;
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
