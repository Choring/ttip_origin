<template>
  <section v-if="events && events.length > 0">
    <!-- 섹션 헤더 -->
    <div class="flex items-center justify-between mb-3 px-1">
      <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
        <span>이번 주 공연·행사</span>
      </h2>
      <Link :href="route('events.index')" class="text-xs font-bold text-gray-400 hover:text-orange-500 transition-colors">
        전체보기 →
      </Link>
    </div>

    <!-- 가로 스크롤 카드 -->
    <div class="relative -mx-4 px-4">
      <button @click="scrollLeft"
        class="hidden md:flex absolute left-0 top-1/2 -translate-y-8 z-10 w-9 h-9 bg-white border border-gray-200 rounded-full shadow-md items-center justify-center hover:bg-orange-50 hover:border-orange-300 transition-all"
      >
        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button @click="scrollRight"
        class="hidden md:flex absolute right-0 top-1/2 -translate-y-8 z-10 w-9 h-9 bg-white border border-gray-200 rounded-full shadow-md items-center justify-center hover:bg-orange-50 hover:border-orange-300 transition-all"
      >
        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </button>

      <div ref="scrollContainer" class="flex gap-4 overflow-x-auto pb-3 no-scrollbar scroll-smooth snap-x snap-mandatory">
        <Link
          v-for="event in events"
          :key="event.event_seq"
          :href="route('events.show', event.event_seq)"
          class="flex-shrink-0 w-56 snap-start group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
        >
          <!-- 이미지 -->
          <div class="relative w-full h-60 overflow-hidden bg-gray-100">
            <img
              :src="event.image"
              :alt="event.subject"
              class="w-full h-full object-cover"
              @error="e => e.target.src = '/images/banner/tour-banner.jpg'"
            />
            <!-- 장르 배지 -->
            <span class="absolute top-2.5 left-2.5 text-[11px] font-black px-2.5 py-1 rounded-full backdrop-blur-sm shadow"
              :class="genreStyle(event.event_gubun)"
            >
              {{ event.event_gubun || '공연' }}
            </span>
            <!-- 무료 배지 -->
            <span v-if="isFree(event.pay)"
              class="absolute top-2.5 right-2.5 text-[11px] font-black bg-green-500 text-white px-2.5 py-1 rounded-full shadow">
              무료
            </span>
          </div>

          <!-- 텍스트 -->
          <div class="p-3">
            <p class="text-sm font-black text-gray-900 line-clamp-2 leading-snug mb-2 group-hover:text-orange-500 transition-colors">
              {{ event.subject }}
            </p>
            <!-- 날짜 범위 -->
            <p class="text-xs text-gray-500 flex items-center gap-1 mb-1">
              <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              {{ formatDateRange(event.start_date, event.end_date) }}
            </p>
            <!-- 장소 -->
            <p class="text-xs text-gray-400 flex items-center gap-1 line-clamp-1">
              <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
              </svg>
              {{ event.place }}
            </p>
          </div>
        </Link>

        <!-- 더보기 카드 -->
        <Link
          :href="route('events.index')"
          class="flex-shrink-0 w-56 snap-start h-60 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-2 hover:border-orange-300 hover:bg-orange-50 transition-all group"
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

// "2026.05.20" ~ "2026.05.26" → "2026.5.20 - 5.26"
const formatDateRange = (start, end) => {
  const parse = (d) => {
    if (!d) return null;
    const parts = d.replace(/\./g, '-').split('-');
    return parts.length >= 3
      ? { year: parts[0], month: parseInt(parts[1]), day: parseInt(parts[2]) }
      : null;
  };
  const s = parse(start);
  const e = parse(end);
  if (!s) return '';
  const startStr = `${s.year}.${s.month}.${s.day}`;
  if (!e || (s.year === e.year && s.month === e.month && s.day === e.day)) return startStr;
  // 같은 연도면 종료일에 연도 생략
  const endStr = s.year === e.year ? `${e.month}.${e.day}` : `${e.year}.${e.month}.${e.day}`;
  return `${startStr} - ${endStr}`;
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
