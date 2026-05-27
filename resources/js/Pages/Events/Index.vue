<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const SESSION_KEY = 'event_list_state';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const props = defineProps({
    events: { type: Array, default: () => [] }
});

// ── 필터 ────────────────────────────────────────────────────────
const FILTERS = [
    { key: 'all', label: '전체' },
    { key: '공연', label: '공연' },
    { key: '전시', label: '전시' },
    { key: '축제', label: '축제' },
    { key: '행사', label: '행사' },
];

const activeFilter = ref('all');
const searchQuery  = ref('');

const filteredEvents = computed(() => {
    let list = props.events;

    // 카테고리 필터
    if (activeFilter.value !== 'all') {
        list = list.filter(e => e.event_gubun === activeFilter.value);
    }

    // 검색어 필터 (제목 + 장소)
    const q = searchQuery.value.trim().toLowerCase();
    if (q) {
        list = list.filter(e =>
            e.subject?.toLowerCase().includes(q) ||
            e.place?.toLowerCase().includes(q)
        );
    }

    return list;
});

// ── 더보기 ───────────────────────────────────────────────────────
const visibleCount = ref(9);
const visibleEvents = computed(() => filteredEvents.value.slice(0, visibleCount.value));
const loadMore = () => { visibleCount.value += 9; };

const setFilter = (key) => {
    activeFilter.value = key;
    visibleCount.value = 9;
};

const clearSearch = () => {
    searchQuery.value = '';
    visibleCount.value = 9;
};

// ── D-day 계산 ───────────────────────────────────────────────────
const getDday = (startDate, endDate) => {
    const today = new Date(); today.setHours(0, 0, 0, 0);
    const start = new Date(startDate);
    const end   = new Date(endDate);
    if (today >= start && today <= end) return { label: '진행중', type: 'ongoing' };
    if (today < start) {
        const diff = Math.ceil((start - today) / (1000 * 60 * 60 * 24));
        return { label: `D-${diff}`, type: 'upcoming' };
    }
    // 종료된 행사
    return { label: '종료', type: 'ended' };
};

// ── 날짜 포맷 ─────────────────────────────────────────────────────
const formatDate = (d) => d ? d.substring(0, 10).replace(/-/g, '.') : '';

// ── 카테고리 색상 ─────────────────────────────────────────────────
const gubunStyle = (gubun) => {
    const map = {
        '공연': 'bg-purple-100 text-purple-700',
        '전시': 'bg-blue-100 text-blue-700',
        '축제': 'bg-orange-100 text-orange-700',
        '행사': 'bg-green-100 text-green-700',
    };
    return map[gubun] || 'bg-gray-100 text-gray-600';
};

// ── 이미지 없을 때 그라데이션 폴백 ───────────────────────────────
const fallbackGradient = (gubun) => {
    const map = {
        '공연': 'from-purple-800 to-indigo-900',
        '전시': 'from-slate-700 to-blue-900',
        '축제': 'from-orange-600 to-rose-700',
        '행사': 'from-teal-600 to-emerald-800',
    };
    return map[gubun] || 'from-gray-700 to-gray-900';
};

// ── 뒤로가기 상태 복원 ──────────────────────────────────────────
onMounted(() => {

    const raw = sessionStorage.getItem(SESSION_KEY);
    if (raw) {
        sessionStorage.removeItem(SESSION_KEY);
        try {
            const state = JSON.parse(raw);
            activeFilter.value = state.activeFilter ?? 'all';
            visibleCount.value = state.visibleCount  ?? 9;
            searchQuery.value  = state.searchQuery   ?? '';
            nextTick(() => {
                window.scrollTo({ top: state.scrollY ?? 0, behavior: 'instant' });
            });
        } catch { /* 복원 실패 시 기본값 유지 */ }
    }
});

const stopRouterListener = router.on('before', (event) => {
    const href = event.detail?.visit?.url?.href ?? event.detail?.visit?.url ?? '';
    if (/\/events\/[^/]+/.test(href)) {
        sessionStorage.setItem(SESSION_KEY, JSON.stringify({
            activeFilter: activeFilter.value,
            visibleCount: visibleCount.value,
            scrollY:      window.scrollY,
            searchQuery:  searchQuery.value,
        }));
    } else {
        sessionStorage.removeItem(SESSION_KEY);
    }
});

onUnmounted(() => {
    stopRouterListener();
});
</script>

<template>
    <Head>
        <title>대구 문화행사 - ttip</title>
        <meta head-key="description"    name="description"    content="대구에서 열리는 공연, 전시, 축제 등 최신 문화행사 정보를 확인하세요." />
        <meta head-key="og:type"        property="og:type"        content="website" />
        <meta head-key="og:site_name"   property="og:site_name"   content="ttip" />
        <meta head-key="og:title"       property="og:title"       content="대구 문화행사 - ttip" />
        <meta head-key="og:description" property="og:description" content="대구에서 열리는 공연, 전시, 축제 등 최신 문화행사 정보를 확인하세요." />
    </Head>

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-16">

            <!-- 헤더 -->
            <div class="mb-10">
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-4">
                    대구 문화행사
                </h1>
                <p class="text-lg text-gray-500 font-medium">
                    대구의 공연, 전시, 축제 정보를 한눈에
                </p>
            </div>

            <!-- 검색창 -->
            <div class="mb-6 relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    v-model="searchQuery"
                    @input="visibleCount = 9"
                    type="text"
                    placeholder="공연명, 장소로 검색..."
                    class="w-full pl-11 pr-10 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary shadow-sm"
                />
                <button
                    v-if="searchQuery"
                    @click="clearSearch"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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

            <!-- 빈 상태 -->
            <div v-if="filteredEvents.length === 0" class="text-center py-24 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="text-6xl mb-4">🎭</div>
                <template v-if="searchQuery">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">"{{ searchQuery }}" 검색 결과가 없습니다</h3>
                    <p class="text-gray-500 mb-4">다른 키워드로 검색해보세요.</p>
                    <button @click="clearSearch" class="px-5 py-2 bg-primary text-white rounded-full text-sm font-bold hover:bg-orange-600 transition-colors">
                        검색 초기화
                    </button>
                </template>
                <template v-else>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">현재 진행 중인 행사가 없습니다</h3>
                    <p class="text-gray-500">잠시 후 다시 확인해주세요.</p>
                </template>
            </div>

            <!-- 카드 그리드 -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link
                    v-for="event in visibleEvents"
                    :key="event.event_seq"
                    :href="route('events.show', event.event_seq)"
                    class="group relative overflow-hidden rounded-3xl transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 flex flex-col cursor-pointer"
                    :class="getDday(event.start_date, event.end_date)?.type === 'ended' ? 'opacity-60 hover:opacity-90' : ''"
                    style="min-height: 280px;"
                >
                    <!-- 이미지가 있는 경우 -->
                    <template v-if="event.image">
                        <!-- 배경 이미지 -->
                        <img
                            :src="event.image"
                            :alt="event.subject"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>

                        <!-- 콘텐츠 -->
                        <div class="relative z-10 flex flex-col h-full p-6">
                            <!-- 상단: D-day + 카테고리 -->
                            <div class="flex items-center gap-2">
                                <span
                                    v-if="getDday(event.start_date, event.end_date)"
                                    class="px-3 py-1 rounded-full text-xs font-black text-white"
                                    :class="{
                                        'bg-primary shadow-lg shadow-primary/50 animate-pulse': getDday(event.start_date, event.end_date).type === 'ongoing',
                                        'bg-gray-900/70 backdrop-blur-sm': getDday(event.start_date, event.end_date).type === 'upcoming',
                                        'bg-gray-500/70 backdrop-blur-sm': getDday(event.start_date, event.end_date).type === 'ended',
                                    }"
                                >
                                    <span v-if="getDday(event.start_date, event.end_date).type === 'ongoing'" class="inline-block w-1.5 h-1.5 rounded-full bg-white mr-1 animate-ping"></span>
                                    {{ getDday(event.start_date, event.end_date).label }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-black text-white bg-white/20 backdrop-blur-sm">
                                    {{ event.event_gubun || '행사' }}
                                </span>
                            </div>

                            <!-- 하단: 제목 + 날짜/장소 -->
                            <div class="mt-auto">
                                <h2 class="font-black text-white text-lg leading-snug mb-3 line-clamp-2 drop-shadow">
                                    {{ event.subject }}
                                </h2>
                                <div class="space-y-1.5">
                                    <p class="text-white/75 text-xs font-medium flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ formatDate(event.start_date) }} ~ {{ formatDate(event.end_date) }}
                                    </p>
                                    <p v-if="event.place" class="text-white/75 text-xs font-medium flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ event.place }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 이미지가 없는 경우: 포스터 스타일 -->
                    <template v-else>
                        <div
                            class="absolute inset-0 bg-gradient-to-br transition-all duration-500"
                            :class="fallbackGradient(event.event_gubun)"
                        ></div>
                        <!-- 배경 장식 원 -->
                        <div class="absolute -right-8 -top-8 w-36 h-36 rounded-full bg-white/10"></div>
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-white/5"></div>

                        <!-- 콘텐츠 -->
                        <div class="relative z-10 flex flex-col h-full p-6">
                            <!-- 상단: D-day + 카테고리 -->
                            <div class="flex items-center gap-2">
                                <span
                                    v-if="getDday(event.start_date, event.end_date)"
                                    class="px-3 py-1 rounded-full text-xs font-black text-white"
                                    :class="{
                                        'bg-primary shadow-lg shadow-black/30 animate-pulse': getDday(event.start_date, event.end_date).type === 'ongoing',
                                        'bg-white/15 backdrop-blur-sm': getDday(event.start_date, event.end_date).type === 'upcoming',
                                        'bg-white/10 backdrop-blur-sm': getDday(event.start_date, event.end_date).type === 'ended',
                                    }"
                                >
                                    <span v-if="getDday(event.start_date, event.end_date).type === 'ongoing'" class="inline-block w-1.5 h-1.5 rounded-full bg-white mr-1 animate-ping"></span>
                                    {{ getDday(event.start_date, event.end_date).label }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-black text-white bg-white/20 backdrop-blur-sm">
                                    {{ event.event_gubun || '행사' }}
                                </span>
                                <span v-if="event.pay === '무료'" class="px-3 py-1 rounded-full text-xs font-black bg-white/20 backdrop-blur-sm text-white">
                                    무료
                                </span>
                            </div>

                            <!-- 하단: 제목 + 날짜/장소 -->
                            <div class="mt-auto">
                                <h2 class="font-black text-white text-xl leading-snug mb-4 drop-shadow">
                                    {{ event.subject }}
                                </h2>
                                <div class="space-y-1.5">
                                    <p class="text-white/70 text-xs font-medium flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ formatDate(event.start_date) }} ~ {{ formatDate(event.end_date) }}
                                    </p>
                                    <p v-if="event.place" class="text-white/70 text-xs font-medium flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ event.place }}
                                    </p>
                                    <p class="text-white/60 text-xs font-semibold mt-2">
                                        자세히 보기 →
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </Link>
            </div>

            <!-- 더보기 버튼 -->
            <div v-if="visibleCount < filteredEvents.length" class="mt-16 flex justify-center">
                <button
                    @click="loadMore"
                    class="px-8 py-4 bg-white border border-gray-200 text-gray-900 rounded-full font-bold shadow-sm hover:shadow-md hover:bg-gray-50 transition-all flex items-center gap-2"
                >
                    더보기 ({{ visibleCount }} / {{ filteredEvents.length }})
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </main>


        <AppFooter />
        <ToastNotification />
    </div>
</template>
