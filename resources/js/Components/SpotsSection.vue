<template>
  <section v-if="spots && spots.length > 0">
    <!-- 섹션 헤더 -->
    <div class="flex items-center justify-between mb-3 px-1">
      <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
        🏔️ <span>대구 관광지</span>
      </h2>
      <Link :href="route('tour.index')" class="text-xs font-bold text-gray-400 hover:text-orange-500 transition-colors">
        전체보기 →
      </Link>
    </div>

    <!-- 가로 스크롤 카드 -->
    <div class="relative -mx-4 px-4">
      <button
        @click="scrollLeft"
        class="hidden md:flex absolute left-0 top-1/2 -translate-y-8 z-10 w-9 h-9 bg-white border border-gray-200 rounded-full shadow-md items-center justify-center hover:bg-orange-50 hover:border-orange-300 transition-all"
      >
        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
      </button>
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
          v-for="spot in spots"
          :key="spot.contentId"
          :href="route('tour.show', spot.contentId)"
          class="flex-shrink-0 w-56 snap-start group"
        >
          <!-- 이미지 (와이드 비율) -->
          <div class="relative w-full h-36 rounded-xl overflow-hidden mb-2 bg-gray-100">
            <img
              :src="spot.image"
              :alt="spot.title"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
              @error="e => e.target.src = '/images/banner/tour-banner.jpg'"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <!-- 제목 오버레이 -->
            <p class="absolute bottom-2 left-2 right-2 text-white text-xs font-black line-clamp-1 drop-shadow">
              {{ spot.title }}
            </p>
          </div>

          <!-- 주소 -->
          <p class="text-xs text-gray-500 line-clamp-1 mt-1">📍 {{ shortAddr(spot.addr1) }}</p>
        </Link>

        <!-- 더보기 카드 -->
        <Link
          :href="route('tour.index')"
          class="flex-shrink-0 w-56 snap-start h-36 rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-2 hover:border-orange-300 hover:bg-orange-50 transition-all group"
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
  spots: { type: Array, default: () => [] },
});

const scrollContainer = ref(null);
const scrollLeft  = () => scrollContainer.value?.scrollBy({ left: -320, behavior: 'smooth' });
const scrollRight = () => scrollContainer.value?.scrollBy({ left:  320, behavior: 'smooth' });

const shortAddr = (addr) => {
  if (!addr) return '';
  return addr.replace('대구광역시 ', '').replace('대구시 ', '');
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
