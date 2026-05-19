<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const props = defineProps({
    spot:         { type: Object, required: true },
    relatedSpots: { type: Array, default: () => [] },
});

const extractDistrict = (addr) => {
    if (!addr) return '';
    const match = addr.match(/(\S+구|\S+군)/);
    return match ? match[0] : '';
};

const fullAddress = computed(() => {
    const parts = [props.spot.addr1, props.spot.addr2].filter(Boolean);
    return parts.join(' ') || '주소 정보 없음';
});

const kakaoMapUrl = computed(() => {
    const { mapX, mapY, title } = props.spot;
    if (!mapX || !mapY) return null;
    return `https://map.kakao.com/link/map/${encodeURIComponent(title)},${mapY},${mapX}`;
});

const kakaoDirectionUrl = computed(() => {
    const { mapX, mapY, title } = props.spot;
    if (!mapX || !mapY) return null;
    return `https://map.kakao.com/link/to/${encodeURIComponent(title)},${mapY},${mapX}`;
});

const staticMapUrl = computed(() => {
    const { mapX, mapY } = props.spot;
    if (!mapX || !mapY) return null;
    return `https://map.kakao.com/link/map/${props.spot.title},${mapY},${mapX}`;
});

const contentTypeLabel = (typeId) => {
    const map = { '12': '관광지', '14': '문화시설', '15': '축제/공연', '28': '레포츠', '38': '쇼핑', '39': '음식점' };
    return map[typeId] || '관광지';
};

// overview HTML 태그 제거
const cleanOverview = computed(() => {
    if (!props.spot.overview) return '';
    return props.spot.overview.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
});
</script>

<template>
    <Head :title="`${spot.title} - 대구 관광`" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <!-- 히어로 이미지 -->
        <div class="relative w-full overflow-hidden" style="height: 50vh; min-height: 320px;">
            <img
                v-if="spot.image"
                :src="spot.image"
                :alt="spot.title"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div v-else class="absolute inset-0 bg-gradient-to-br from-gray-400 to-gray-600"></div>

            <!-- 그라데이션 오버레이 -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/10"></div>

            <!-- 뒤로가기 -->
            <div class="absolute top-6 left-6 z-10">
                <Link
                    :href="route('tour.index')"
                    class="inline-flex items-center gap-2 text-white/90 hover:text-white font-bold text-sm bg-black/20 backdrop-blur-sm px-4 py-2 rounded-full transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    대구 관광
                </Link>
            </div>

            <!-- 타이틀 정보 -->
            <div class="absolute bottom-0 left-0 right-0 p-8 z-10">
                <span class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-black px-3 py-1 rounded-full mb-3">
                    MUST VISIT
                </span>
                <h1 class="text-3xl md:text-5xl font-black text-white leading-tight drop-shadow-lg mb-2">
                    {{ spot.title }}
                </h1>
                <p class="text-white/80 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ extractDistrict(spot.addr1) || '대구광역시' }}
                </p>
            </div>
        </div>

        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-16">

            <!-- 본문 2단 레이아웃 -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-12">

                <!-- 좌측: 상세 정보 카드 -->
                <div class="lg:col-span-3 bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-xl font-black text-gray-900 mb-6">상세 정보</h2>

                    <div class="space-y-5">
                        <!-- 주소 -->
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">주소</p>
                                <p class="text-gray-800 font-semibold">{{ fullAddress }}</p>
                            </div>
                        </div>

                        <!-- 전화번호 -->
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">연락처</p>
                                <p class="text-gray-800 font-semibold">{{ spot.tel || '정보 없음' }}</p>
                            </div>
                        </div>

                        <!-- 분류 -->
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">분류</p>
                                <p class="text-gray-800 font-semibold">{{ contentTypeLabel(spot.contentTypeId) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- 길찾기 버튼 -->
                    <a
                        v-if="kakaoDirectionUrl"
                        :href="kakaoDirectionUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-8 w-full flex items-center justify-center gap-2 bg-primary hover:bg-[#E65300] text-white font-black py-4 rounded-2xl transition-colors shadow-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        길찾기
                    </a>
                </div>

                <!-- 우측: 지도 -->
                <div class="lg:col-span-2 bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm" style="min-height: 320px;">
                    <a
                        v-if="kakaoMapUrl"
                        :href="kakaoMapUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block w-full h-full relative group"
                        style="min-height: 320px;"
                    >
                        <!-- 카카오맵 정적 지도 이미지 대신 안내 UI -->
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="font-black text-gray-700 text-sm">{{ spot.title }}</p>
                                <p class="text-gray-500 text-xs mt-0.5">{{ extractDistrict(spot.addr1) }}</p>
                            </div>
                            <span class="text-xs text-primary font-bold bg-orange-50 px-3 py-1.5 rounded-full group-hover:bg-primary group-hover:text-white transition-colors">
                                카카오맵에서 보기 →
                            </span>
                        </div>
                        <p class="absolute bottom-3 right-3 text-[10px] text-gray-400 font-bold uppercase tracking-wider">KAKAO MAPS</p>
                    </a>
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm font-medium" style="min-height: 320px;">
                        지도 정보 없음
                    </div>
                </div>
            </div>

            <!-- 관광지 설명 -->
            <div v-if="cleanOverview" class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm mb-12">
                <p class="text-gray-700 leading-relaxed font-medium">{{ cleanOverview }}</p>
            </div>

            <!-- 다른 관광지 둘러보기 -->
            <div v-if="relatedSpots.length > 0">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-black text-gray-900">다른 관광지 둘러보기</h2>
                    <Link :href="route('tour.index')" class="text-sm font-bold text-primary hover:underline">
                        전체보기
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                    <Link
                        v-for="related in relatedSpots"
                        :key="related.contentId"
                        :href="route('tour.show', related.contentId)"
                        class="group relative overflow-hidden rounded-2xl bg-gray-200 border border-black/5 transition-all hover:shadow-lg hover:-translate-y-1"
                        style="min-height: 200px;"
                    >
                        <img
                            v-if="related.image || related.thumbnail"
                            :src="related.image || related.thumbnail"
                            :alt="related.title"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 z-10">
                            <p class="text-[10px] font-black text-primary/90 uppercase tracking-widest mb-1">
                                {{ contentTypeLabel(related.contentTypeId) }}
                            </p>
                            <h3 class="text-white font-black text-base leading-snug">{{ related.title }}</h3>
                            <p class="text-white/60 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ extractDistrict(related.addr1) || '대구광역시' }}
                            </p>
                        </div>
                    </Link>
                </div>

                <div class="flex justify-center">
                    <Link
                        :href="route('tour.index')"
                        class="px-8 py-3.5 border-2 border-primary text-primary font-black rounded-full hover:bg-primary hover:text-white transition-all"
                    >
                        목록으로 돌아가기
                    </Link>
                </div>
            </div>
        </main>

        <AppFooter />
        <ToastNotification />
    </div>
</template>
