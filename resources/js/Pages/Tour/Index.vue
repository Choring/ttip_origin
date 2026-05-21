<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import LazyImage from '@/Components/LazyImage.vue';

const SESSION_KEY = 'tour_list_state';

const props = defineProps({
    spots: { type: Array, default: () => [] }
});

const DISTRICTS = [
    { key: 'all',  label: '전체' },
    { key: '중구',  label: '중구' },
    { key: '동구',  label: '동구' },
    { key: '서구',  label: '서구' },
    { key: '남구',  label: '남구' },
    { key: '북구',  label: '북구' },
    { key: '수성구', label: '수성구' },
    { key: '달서구', label: '달서구' },
    { key: '달성군', label: '달성군' },
];

const activeCategory = ref('all');
const visibleCount = ref(12);

// 셔플된 spots (페이지 진입 시마다 랜덤 순서)
const shuffledSpots = ref([]);
const shuffleArray = (arr) => [...arr].sort(() => Math.random() - 0.5);

const filteredSpots = computed(() => {
    const base = shuffledSpots.value.length ? shuffledSpots.value : props.spots;
    if (activeCategory.value === 'all') return base;
    return base.filter(spot => spot.addr1?.includes(activeCategory.value));
});

const visibleSpots = computed(() => filteredSpots.value.slice(0, visibleCount.value));

const loadMore = () => { visibleCount.value += 12; };

const setCategory = (key) => {
    activeCategory.value = key;
    visibleCount.value = 12;
};

// 주소에서 구 이름만 추출 (예: "대구광역시 수성구 ..." → "수성구")
const extractDistrict = (addr) => {
    if (!addr) return '';
    const match = addr.match(/(\S+구|\S+군)/);
    return match ? match[0] : '';
};

// 스크롤 상단 버튼
const showScrollTop = ref(false);
const handleScroll = () => {
    showScrollTop.value = (window.scrollY || document.documentElement.scrollTop) > 150;
};
const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

// ── 뒤로가기 상태 복원 ──────────────────────────────────────────
onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });

    const raw = sessionStorage.getItem(SESSION_KEY);

    if (raw) {
        // 뒤로가기: 저장된 셔플 순서 + 상태 그대로 복원
        sessionStorage.removeItem(SESSION_KEY);
        try {
            const state = JSON.parse(raw);
            // 저장된 contentId 순서로 배열 재정렬
            if (state.shuffledIds?.length) {
                const spotMap = Object.fromEntries(props.spots.map(s => [String(s.contentId), s]));
                shuffledSpots.value = state.shuffledIds.map(id => spotMap[id]).filter(Boolean);
            } else {
                shuffledSpots.value = shuffleArray(props.spots);
            }
            visibleCount.value = state.visibleCount ?? 12;
            activeCategory.value = state.activeCategory ?? 'all';
            nextTick(() => {
                window.scrollTo({ top: state.scrollY ?? 0, behavior: 'instant' });
            });
        } catch {
            shuffledSpots.value = shuffleArray(props.spots);
        }
    } else {
        // 새 진입: 랜덤 셔플
        shuffledSpots.value = shuffleArray(props.spots);
    }
});

// 페이지 이탈 직전 처리:
// - 관광지 상세로 이동 → 현재 상태 + 셔플 순서 저장
// - 다른 페이지로 이동 → 저장된 상태 제거
const stopRouterListener = router.on('before', (event) => {
    const href = event.detail?.visit?.url?.href ?? event.detail?.visit?.url ?? '';
    if (/\/tour\/[^/]+/.test(href)) {
        sessionStorage.setItem(SESSION_KEY, JSON.stringify({
            visibleCount: visibleCount.value,
            activeCategory: activeCategory.value,
            scrollY: window.scrollY,
            shuffledIds: shuffledSpots.value.map(s => String(s.contentId)),
        }));
    } else {
        sessionStorage.removeItem(SESSION_KEY);
    }
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    stopRouterListener();
});
</script>

<template>
    <Head title="대구 관광" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-16">

            <!-- 헤더 -->
            <div class="mb-10">
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-4">
                    대구 관광
                </h1>
                <p class="text-lg text-gray-500 font-medium">
                    대구의 숨겨진 매력을 발견하세요.
                </p>
            </div>

            <!-- 지역구 필터 탭 -->
            <!-- 모바일: 가로 슬라이드 / 데스크탑: 줄바꿈 -->
            <div class="mb-10 -mx-4 px-4 md:mx-0 md:px-0 relative">
                <!-- 모바일 슬라이드 힌트: 오른쪽 페이드 -->
                <div class="md:hidden absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none z-10"></div>
                <div class="flex gap-2 md:flex-wrap overflow-x-auto md:overflow-visible no-scrollbar pb-1 md:pb-0">
                    <button
                        v-for="district in DISTRICTS"
                        :key="district.key"
                        @click="setCategory(district.key)"
                        class="flex-shrink-0 px-5 py-2.5 rounded-full font-bold text-sm transition-all"
                        :class="activeCategory === district.key
                            ? 'bg-primary text-white shadow-md'
                            : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                    >
                        {{ district.label }}
                    </button>
                </div>
            </div>

            <!-- 빈 상태 -->
            <div v-if="filteredSpots.length === 0" class="text-center py-24 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="text-6xl mb-4">🗺️</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">관광지 정보를 불러오는 중입니다</h3>
                <p class="text-gray-500">잠시 후 다시 확인해주세요.</p>
            </div>

            <!-- 그리드 -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link
                    v-for="(spot, index) in visibleSpots"
                    :key="spot.contentId"
                    :href="route('tour.show', spot.contentId)"
                    class="group relative overflow-hidden rounded-3xl bg-gray-200 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 border border-black/5 flex flex-col"
                    :class="index === 0 ? 'md:col-span-2 md:row-span-2' : ''"
                    :style="index === 0 ? 'min-height: 520px;' : 'min-height: 260px;'"
                >
                    <!-- 이미지 (LazyImage: viewport 400px 전 로드 + fade-in) -->
                    <LazyImage
                        v-if="spot.image || spot.thumbnail"
                        :src="spot.image || spot.thumbnail"
                        :alt="spot.title"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105"
                    />
                    <div v-else class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-500 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <!-- 그라데이션 오버레이 -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                    <!-- 텍스트 -->
                    <div class="relative z-10 mt-auto p-6">
                        <span v-if="index === 0" class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-black px-3 py-1 rounded-full mb-3">
                            MUST VISIT
                        </span>
                        <h2 class="text-white font-black leading-tight drop-shadow-sm"
                            :class="index === 0 ? 'text-2xl md:text-3xl' : 'text-lg'"
                        >
                            {{ spot.title }}
                        </h2>
                        <p class="text-white/70 text-sm font-medium mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ extractDistrict(spot.addr1) || '대구광역시' }}
                        </p>
                    </div>
                </Link>
            </div>

            <!-- 더보기 버튼 -->
            <div v-if="visibleCount < filteredSpots.length" class="mt-16 flex justify-center">
                <button
                    @click="loadMore"
                    class="px-8 py-4 bg-white border border-gray-200 text-gray-900 rounded-full font-bold shadow-sm hover:shadow-md hover:bg-gray-50 transition-all flex items-center gap-2"
                >
                    더보기 ({{ visibleCount }} / {{ filteredSpots.length }})
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
