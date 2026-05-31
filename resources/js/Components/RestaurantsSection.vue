<template>
  <section v-if="restaurants && restaurants.length > 0">
    <!-- 섹션 헤더 -->
    <div class="flex items-center justify-between mb-3 px-1">
      <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
        🍽️ <span>추천 맛집</span>
      </h2>
      <Link :href="route('restaurants.index')" class="text-xs font-bold text-gray-400 hover:text-orange-500 transition-colors">
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
          v-for="restaurant in restaurants"
          :key="restaurant.contentId"
          :href="route('restaurants.show', restaurant.contentId)"
          class="flex-shrink-0 w-48 snap-start group"
        >
          <!-- 이미지 -->
          <div class="relative w-full h-44 rounded-xl overflow-hidden mb-2 bg-gray-100">
            <img
              :src="restaurant.image"
              :alt="restaurant.title"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
              @error="e => e.target.src = '/images/banner/tour-banner.jpg'"
            />
            <!-- 카테고리 배지 -->
            <span class="absolute bottom-2 left-2 text-[10px] font-black bg-black/60 text-white px-2 py-0.5 rounded-full backdrop-blur-sm">
              {{ restaurant.category || '맛집' }}
            </span>
          </div>

          <!-- 텍스트 -->
          <p class="text-sm font-black text-gray-900 line-clamp-2 leading-snug mb-0.5 group-hover:text-orange-500 transition-colors">
            {{ restaurant.title }}
          </p>
          <p class="text-xs text-gray-500 line-clamp-1">📍 {{ shortAddress(restaurant.address) }}</p>
        </Link>

        <!-- 더보기 카드 -->
        <Link
          :href="route('restaurants.index')"
          class="flex-shrink-0 w-48 snap-start h-44 rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-2 hover:border-orange-300 hover:bg-orange-50 transition-all group"
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
  restaurants: { type: Array, default: () => [] },
});

const scrollContainer = ref(null);
const scrollLeft  = () => scrollContainer.value?.scrollBy({ left: -320, behavior: 'smooth' });
const scrollRight = () => scrollContainer.value?.scrollBy({ left:  320, behavior: 'smooth' });

const shortAddress = (address) => {
  if (!address) return '';
  // "대구광역시 중구 동성로" → "중구 동성로"
  return address.replace('대구광역시 ', '').replace('대구시 ', '');
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
