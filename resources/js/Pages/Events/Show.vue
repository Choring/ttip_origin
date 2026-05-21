<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';

const props = defineProps({
    event:         { type: Object, required: true },
    relatedEvents: { type: Array,  default: () => [] },
});

// ── 공유 ──────────────────────────────────────────────────────────
const copied = ref(false);
const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        copied.value = true;
        setTimeout(() => { copied.value = false; }, 2000);
    } catch {
        // 구형 브라우저 폴백
        const el = document.createElement('textarea');
        el.value = window.location.href;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        copied.value = true;
        setTimeout(() => { copied.value = false; }, 2000);
    }
};

const shareNative = async () => {
    if (navigator.share) {
        await navigator.share({
            title: props.event.subject,
            text:  `${props.event.subject} | ttip 대구 문화행사`,
            url:   window.location.href,
        });
    } else {
        copyLink();
    }
};

// ── 카카오맵 검색 URL ────────────────────────────────────────────
const kakaoMapUrl = computed(() =>
    props.event.place
        ? `https://map.kakao.com/?q=${encodeURIComponent(props.event.place)}`
        : null
);

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
    return { label: '종료', type: 'ended' };
};

const dday = computed(() => getDday(props.event.startDate, props.event.endDate));

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
</script>

<template>
    <Head :title="`${event.subject} - 대구 문화행사`" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <!-- ── 히어로 섹션 ─────────────────────────────────────── -->
        <div class="relative w-full overflow-hidden" style="height: 60vh; min-height: 380px; max-height: 600px;">
            <!-- 배경 -->
            <template v-if="event.image">
                <img :src="event.image" class="absolute inset-0 w-full h-full object-cover scale-110 blur-md opacity-50" />
                <div class="absolute inset-0 bg-black/55"></div>
                <img :src="event.image" :alt="event.subject"
                    class="absolute inset-0 w-full h-full object-contain" />
            </template>
            <template v-else>
                <div class="absolute inset-0 bg-gradient-to-br" :class="fallbackGradient(event.eventGubun)"></div>
                <!-- 장식 원들 -->
                <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full bg-white/5"></div>
                <div class="absolute right-32 top-8 w-40 h-40 rounded-full bg-white/5"></div>
                <div class="absolute -left-10 bottom-0 w-64 h-64 rounded-full bg-black/10"></div>
            </template>

            <!-- 하단 그라데이션 오버레이 -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

            <!-- 뒤로가기 (히어로 상단) -->
            <div class="absolute top-6 left-0 right-0 px-6 md:px-12 lg:px-20">
                <Link :href="route('events.index')"
                    class="inline-flex items-center gap-1.5 text-sm text-white/70 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    문화행사 목록
                </Link>
            </div>

            <!-- 타이틀 영역 (히어로 하단) -->
            <div class="absolute bottom-0 left-0 right-0 px-6 pb-10 md:px-12 lg:px-20">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-3 py-1 rounded-full text-xs font-black text-white"
                        :class="{
                            'bg-primary shadow-lg shadow-primary/50': dday.type === 'ongoing',
                            'bg-white/25 backdrop-blur-sm': dday.type === 'upcoming',
                            'bg-white/15 backdrop-blur-sm': dday.type === 'ended',
                        }"
                    >
                        <span v-if="dday.type === 'ongoing'" class="inline-block w-1.5 h-1.5 rounded-full bg-white mr-1 animate-ping"></span>
                        {{ dday.label }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-black text-white bg-white/25 backdrop-blur-sm">
                        {{ event.eventGubun || '행사' }}
                    </span>
                    <span v-if="event.pay === '무료'" class="px-3 py-1 rounded-full text-xs font-black text-white bg-green-500/80 backdrop-blur-sm">
                        무료
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-white leading-tight drop-shadow-lg max-w-3xl">
                    {{ event.subject }}
                </h1>
                <!-- 기간·장소 요약 -->
                <div class="flex flex-wrap items-center gap-4 mt-3">
                    <span class="flex items-center gap-1.5 text-white/70 text-sm font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ formatDate(event.startDate) }} ~ {{ formatDate(event.endDate) }}
                    </span>
                    <span v-if="event.place" class="flex items-center gap-1.5 text-white/70 text-sm font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ event.place }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ── 본문 ───────────────────────────────────────────── -->
        <main class="w-full px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-12">

            <!-- ── 행사 정보 바 ────────────────────────────────── -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 mb-6 flex flex-wrap items-center gap-6 md:gap-10">
                <!-- 요금 -->
                <div v-if="event.pay" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-semibold">요금</p>
                        <p class="text-sm font-black" :class="event.pay === '무료' ? 'text-green-600' : 'text-gray-900'">{{ event.pay }}</p>
                    </div>
                </div>

                <!-- 분류 -->
                <div v-if="event.eventGubun" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-semibold">분류</p>
                        <span class="text-xs font-black px-2.5 py-1 rounded-full" :class="gubunStyle(event.eventGubun)">
                            {{ event.eventGubun }}
                        </span>
                    </div>
                </div>

                <!-- 오른쪽 버튼 영역 -->
                <div class="flex items-center gap-2 ml-auto flex-shrink-0">
                    <!-- 공유 버튼 -->
                    <button @click="shareNative"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        공유
                    </button>
                    <!-- 링크 복사 버튼 -->
                    <button @click="copyLink"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-bold text-sm transition-all"
                        :class="copied
                            ? 'bg-green-500 text-white border border-green-500'
                            : 'bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100'">
                        <svg v-if="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ copied ? '복사됨!' : '링크 복사' }}
                    </button>
                    <!-- 홈페이지 버튼 -->
                    <a v-if="event.homepage" :href="event.homepage" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm hover:bg-orange-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        공식 홈페이지
                    </a>
                </div>
            </div>

            <!-- ── 행사 소개 ───────────────────────────────────── -->
            <div v-if="event.content" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 mb-6">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-5">행사 소개</h2>
                <div class="text-sm text-gray-700 leading-relaxed" v-html="event.content"></div>
            </div>

            <!-- ── 장소 지도 ──────────────────────────────────── -->
            <div v-if="event.place" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-xs font-black text-gray-400 uppercase tracking-wider">장소 안내</span>
                    </div>
                    <a v-if="kakaoMapUrl" :href="kakaoMapUrl" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-400 text-yellow-900 rounded-lg font-black text-xs hover:bg-yellow-300 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        카카오맵에서 길찾기
                    </a>
                </div>
                <div class="px-6 py-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-black text-gray-900 mb-0.5">{{ event.place }}</p>
                        <p class="text-xs text-gray-400 font-medium">행사 장소</p>
                    </div>
                </div>
            </div>

            <!-- ── 관련 행사 ───────────────────────────────────── -->
            <div v-if="relatedEvents.length > 0" class="mb-8">
                <h2 class="text-lg font-black text-gray-900 mb-5">🎭 다른 행사도 확인해보세요</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Link
                        v-for="rel in relatedEvents"
                        :key="rel.eventSeq"
                        :href="route('events.show', rel.eventSeq)"
                        class="group relative overflow-hidden rounded-2xl transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 flex flex-col"
                        :class="getDday(rel.startDate, rel.endDate)?.type === 'ended' ? 'opacity-60' : ''"
                        style="min-height: 180px;"
                    >
                        <template v-if="rel.image">
                            <img :src="rel.image" :alt="rel.subject"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        </template>
                        <template v-else>
                            <div class="absolute inset-0 bg-gradient-to-br" :class="fallbackGradient(rel.eventGubun)"></div>
                        </template>
                        <div class="relative z-10 mt-auto p-4">
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full text-white mb-1.5 inline-block"
                                :class="getDday(rel.startDate, rel.endDate)?.type === 'ongoing' ? 'bg-primary' : 'bg-white/20'"
                            >
                                {{ getDday(rel.startDate, rel.endDate)?.label }}
                            </span>
                            <p class="text-white font-black text-sm leading-snug line-clamp-2">{{ rel.subject }}</p>
                            <p class="text-white/60 text-[11px] mt-1">{{ formatDate(rel.startDate) }} ~ {{ formatDate(rel.endDate) }}</p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- ── 목록으로 ────────────────────────────────────── -->
            <div class="flex justify-center pt-2 pb-4">
                <Link :href="route('events.index')"
                    class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-50 transition-colors inline-flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    문화행사 목록으로
                </Link>
            </div>

        </main>

        <AppFooter />
    </div>
</template>
