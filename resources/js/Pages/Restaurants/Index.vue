<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const props = defineProps({
    restaurants: { type: Array, default: () => [] }
});

// ── 필터 ────────────────────────────────────────────────────────
const FILTERS = [
    { key: 'all',       label: '전체' },
    { key: '한식',      label: '한식' },
    { key: '카페',      label: '카페' },
    { key: '양식',      label: '양식' },
    { key: '일식',      label: '일식' },
    { key: '중식',      label: '중식' },
    { key: '이색음식점', label: '이색음식점' },
];

const activeFilter = ref('all');

const filteredRestaurants = computed(() => {
    if (activeFilter.value === 'all') return props.restaurants;
    return props.restaurants.filter(r => r.category === activeFilter.value);
});

// ── 더보기 ───────────────────────────────────────────────────────
const visibleCount = ref(12);
const visibleRestaurants = computed(() => filteredRestaurants.value.slice(0, visibleCount.value));
const loadMore = () => { visibleCount.value += 12; };

const setFilter = (key) => {
    activeFilter.value = key;
    visibleCount.value = 12;
};

// ── 카테고리 스타일 ──────────────────────────────────────────────
const categoryStyle = (cat) => {
    const map = {
        '한식':     'bg-red-100 text-red-700',
        '양식':     'bg-yellow-100 text-yellow-700',
        '일식':     'bg-pink-100 text-pink-700',
        '중식':     'bg-orange-100 text-orange-700',
        '카페':     'bg-amber-100 text-amber-700',
        '이색음식점': 'bg-purple-100 text-purple-700',
        '기타':     'bg-gray-100 text-gray-600',
    };
    return map[cat] || 'bg-gray-100 text-gray-600';
};

// ── 카테고리별 그라데이션 폴백 ────────────────────────────────────
const fallbackGradient = (cat) => {
    const map = {
        '한식':     'from-red-700 to-rose-900',
        '양식':     'from-yellow-600 to-orange-700',
        '일식':     'from-pink-700 to-rose-800',
        '중식':     'from-orange-700 to-red-800',
        '카페':     'from-amber-600 to-yellow-800',
        '이색음식점': 'from-purple-700 to-indigo-900',
        '기타':     'from-gray-600 to-gray-800',
    };
    return map[cat] || 'from-gray-600 to-gray-800';
};

// ── 카테고리 이모지 ───────────────────────────────────────────────
const categoryEmoji = (cat) => {
    const map = {
        '한식':     '🍚',
        '양식':     '🍝',
        '일식':     '🍣',
        '중식':     '🥟',
        '카페':     '☕',
        '이색음식점': '🌮',
        '기타':     '🍽️',
    };
    return map[cat] || '🍽️';
};

// ── 스크롤 상단 버튼 ──────────────────────────────────────────────
const showScrollTop = ref(false);
const handleScroll = () => { showScrollTop.value = window.scrollY > 150; };
const scrollToTop  = () => window.scrollTo({ top: 0, behavior: 'smooth' });

onMounted(()  => window.addEventListener('scroll', handleScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', handleScroll));
</script>

<template>
    <Head title="대구 맛집" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-16">

            <!-- 헤더 -->
            <div class="mb-10">
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-4">
                    대구 맛집
                </h1>
                <p class="text-lg text-gray-500 font-medium">
                    대구의 한식, 양식, 일식, 중식, 카페까지 한눈에
                </p>
            </div>

            <!-- 필터 탭 -->
            <div class="mb-10 -mx-4 px-4 md:mx-0 md:px-0 relative">
                <div class="md:hidden absolute right-0 top-0 bottom-0 w-10 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none z-10"></div>
                <div class="flex gap-2 overflow-x-auto md:flex-wrap md:overflow-visible no-scrollbar pb-1 md:pb-0">
                    <button
                        v-for="f in FILTERS"
                        :key="f.key"
                        @click="setFilter(f.key)"
                        class="flex-shrink-0 px-5 py-2.5 rounded-full font-bold text-sm transition-all"
                        :class="activeFilter === f.key
                            ? 'bg-primary text-white shadow-md'
                            : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                    >
                        {{ f.label }}
                    </button>
                </div>
            </div>

            <!-- 건수 표시 -->
            <p class="mb-6 text-sm text-gray-400 font-medium">
                {{ filteredRestaurants.length.toLocaleString() }}곳
            </p>

            <!-- 빈 상태 -->
            <div v-if="filteredRestaurants.length === 0" class="text-center py-24 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="text-6xl mb-4">🍽️</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">등록된 맛집이 없습니다</h3>
                <p class="text-gray-500">잠시 후 다시 확인해주세요.</p>
            </div>

            <!-- 카드 그리드 -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <Link
                    v-for="restaurant in visibleRestaurants"
                    :key="restaurant.content_id"
                    :href="route('restaurants.show', restaurant.content_id)"
                    class="group relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col cursor-pointer"
                >
                    <!-- 이미지 영역 -->
                    <div class="relative overflow-hidden" style="height: 180px;">
                        <!-- 이미지 있는 경우 -->
                        <template v-if="restaurant.image">
                            <img
                                :src="restaurant.image"
                                :alt="restaurant.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </template>
                        <!-- 이미지 없는 경우: 그라데이션 -->
                        <template v-else>
                            <div
                                class="w-full h-full bg-gradient-to-br transition-all duration-500"
                                :class="fallbackGradient(restaurant.category)"
                            ></div>
                            <!-- 장식 원 -->
                            <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-white/10"></div>
                            <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-white/5"></div>
                            <!-- 이모지 -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-5xl opacity-80 select-none">{{ categoryEmoji(restaurant.category) }}</span>
                            </div>
                        </template>

                        <!-- 카테고리 뱃지 -->
                        <span
                            class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-black"
                            :class="restaurant.image ? 'bg-white/90 backdrop-blur-sm ' + categoryStyle(restaurant.category) : 'bg-white/20 text-white'"
                        >
                            {{ restaurant.category }}
                        </span>
                    </div>

                    <!-- 정보 영역 -->
                    <div class="p-4 flex flex-col gap-1.5 flex-1">
                        <h2 class="font-black text-gray-900 text-base leading-snug line-clamp-2">
                            {{ restaurant.title }}
                        </h2>

                        <p v-if="restaurant.address" class="text-gray-500 text-xs font-medium flex items-start gap-1.5 mt-0.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="line-clamp-1">{{ restaurant.address }}</span>
                        </p>

                        <p v-if="restaurant.tel" class="text-gray-400 text-xs font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ restaurant.tel }}
                        </p>

                        <p class="text-primary text-xs font-bold mt-auto pt-1">
                            자세히 보기 →
                        </p>
                    </div>
                </Link>
            </div>

            <!-- 더보기 버튼 -->
            <div v-if="visibleCount < filteredRestaurants.length" class="mt-16 flex justify-center">
                <button
                    @click="loadMore"
                    class="px-8 py-4 bg-white border border-gray-200 text-gray-900 rounded-full font-bold shadow-sm hover:shadow-md hover:bg-gray-50 transition-all flex items-center gap-2"
                >
                    더보기 ({{ visibleCount }} / {{ filteredRestaurants.length }})
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </main>

        <!-- 스크롤 상단 버튼 -->
        <button
            @click="scrollToTop"
            class="fixed bottom-8 right-8 z-50 p-4 rounded-full bg-primary text-white shadow-xl hover:bg-[#E65300] hover:-translate-y-1 transition-all duration-300"
            :class="showScrollTop ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10 pointer-events-none'"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
            </svg>
        </button>

        <AppFooter />
        <ToastNotification />
    </div>
</template>
