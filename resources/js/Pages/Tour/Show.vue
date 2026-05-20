<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

const props = defineProps({
    spot:               { type: Object, required: true },
    relatedSpots:       { type: Array,  default: () => [] },
    nearbyRestaurants:  { type: Array,  default: () => [] },
});

// 이미지 갤러리
const activeImage = ref(props.spot.image || props.spot.thumbnail || null);
const allImages = computed(() => {
    const imgs = [];
    if (props.spot.image) imgs.push(props.spot.image);
    if (props.spot.extraImages?.length) {
        props.spot.extraImages.forEach(url => {
            if (url && !imgs.includes(url)) imgs.push(url);
        });
    }
    return imgs;
});

// ── 라이트박스 ──────────────────────────────────────────────────
const lightboxOpen  = ref(false);
const lightboxIndex = ref(0);

const openLightbox = (index) => {
    if (!allImages.value.length) return;
    lightboxIndex.value = index;
    lightboxOpen.value  = true;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    lightboxOpen.value = false;
    document.body.style.overflow = '';
};

const prevImage = () => {
    lightboxIndex.value = (lightboxIndex.value - 1 + allImages.value.length) % allImages.value.length;
};

const nextImage = () => {
    lightboxIndex.value = (lightboxIndex.value + 1) % allImages.value.length;
};

const onKeydown = (e) => {
    if (!lightboxOpen.value) return;
    if (e.key === 'Escape')     closeLightbox();
    if (e.key === 'ArrowLeft')  prevImage();
    if (e.key === 'ArrowRight') nextImage();
};

onMounted(()  => window.addEventListener('keydown', onKeydown));
onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
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

const contentTypeLabel = (typeId) => {
    const map = { '12': '관광지', '14': '문화시설', '15': '축제/공연', '28': '레포츠', '38': '쇼핑', '39': '음식점' };
    return map[typeId] || '관광지';
};

const cleanOverview = computed(() => {
    if (!props.spot.overview) return '';
    return props.spot.overview.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
});

// 이용 정보 항목 (값이 있는 것만 표시)
const infoItems = computed(() => {
    const items = [
        { icon: 'clock',   label: '이용시간', value: props.spot.usetime },
        { icon: 'calendar',label: '휴무일',   value: props.spot.restdate },
        { icon: 'ticket',  label: '이용요금', value: props.spot.usefee },
        { icon: 'car',     label: '주차',     value: props.spot.parking },
        { icon: 'paw',     label: '반려동물', value: props.spot.chkpet },
        { icon: 'baby',    label: '유모차',   value: props.spot.chkbabycarriage },
    ];
    return items.filter(i => i.value && i.value.trim() !== '');
});
</script>

<template>
    <Head :title="`${spot.title} - 대구 관광`" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <!-- 히어로 이미지 (클릭 시 라이트박스 오픈) -->
        <div
            class="relative w-full overflow-hidden bg-black"
            style="height: 55vh; min-height: 340px;"
            :class="allImages.length ? 'cursor-zoom-in' : ''"
            @click="allImages.length && openLightbox(allImages.indexOf(activeImage) >= 0 ? allImages.indexOf(activeImage) : 0)"
        >
            <!-- 블러 배경: 이미지 여백을 자연스럽게 채움 -->
            <img
                v-if="activeImage"
                :src="activeImage"
                :alt="''"
                aria-hidden="true"
                class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-50 transition-all duration-500"
            />
            <!-- 메인 이미지: 원본 비율 그대로 표시 -->
            <img
                v-if="activeImage"
                :src="activeImage"
                :alt="spot.title"
                class="absolute inset-0 w-full h-full object-contain transition-all duration-500 pointer-events-none"
            />
            <div v-else class="absolute inset-0 bg-gradient-to-br from-orange-400 to-orange-600"></div>

            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-black/10"></div>

            <!-- 뒤로가기 -->
            <div class="absolute top-6 left-6 z-10" @click.stop>
                <Link
                    :href="route('tour.index')"
                    class="inline-flex items-center gap-2 text-white/90 hover:text-white font-bold text-sm bg-black/25 backdrop-blur-sm px-4 py-2 rounded-full transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    대구 관광
                </Link>
            </div>

            <!-- 타이틀 -->
            <div class="absolute bottom-0 left-0 right-0 px-8 pb-8 z-10">
                <span class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-black px-3 py-1 rounded-full mb-3">
                    MUST VISIT
                </span>
                <h1 class="text-3xl md:text-5xl font-black text-white leading-tight drop-shadow-lg mb-2">
                    {{ spot.title }}
                </h1>
                <p class="text-white/80 font-medium flex items-center gap-1.5 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ extractDistrict(spot.addr1) || '대구광역시' }}
                </p>
            </div>
        </div>

        <!-- 이미지 갤러리 썸네일 (추가 이미지가 있을 때만) -->
        <div v-if="allImages.length > 1" class="bg-black flex gap-1.5 px-4 py-2 overflow-x-auto">
            <button
                v-for="(img, i) in allImages"
                :key="i"
                @click="activeImage = img"
                class="flex-shrink-0 w-16 h-12 rounded-lg overflow-hidden border-2 transition-all"
                :class="activeImage === img ? 'border-primary' : 'border-transparent opacity-60 hover:opacity-100'"
            >
                <img :src="img" class="w-full h-full object-cover" />
            </button>
        </div>

        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-14">

            <!-- 본문 2단 레이아웃 -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

                <!-- 좌측: 기본 정보 카드 -->
                <div class="lg:col-span-3 bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-lg font-black text-gray-900 mb-5">기본 정보</h2>

                    <div class="space-y-4">
                        <!-- 주소 -->
                        <div class="flex gap-4 items-start">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">주소</p>
                                <p class="text-gray-800 font-semibold text-sm">{{ fullAddress }}</p>
                            </div>
                        </div>

                        <!-- 전화번호 -->
                        <div v-if="spot.tel" class="flex gap-4 items-start">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">연락처</p>
                                <a :href="`tel:${spot.tel}`" class="text-gray-800 font-semibold text-sm hover:text-primary">{{ spot.tel }}</a>
                            </div>
                        </div>

                        <!-- 분류 -->
                        <div class="flex gap-4 items-start">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">분류</p>
                                <p class="text-gray-800 font-semibold text-sm">{{ contentTypeLabel(spot.contentTypeId) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- 길찾기 버튼 -->
                    <a
                        v-if="kakaoDirectionUrl"
                        :href="kakaoDirectionUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-7 w-full flex items-center justify-center gap-2 bg-primary hover:bg-[#E65300] text-white font-black py-3.5 rounded-2xl transition-colors shadow-sm text-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        카카오맵으로 길찾기
                    </a>
                </div>

                <!-- 우측: 지도 -->
                <div class="lg:col-span-2 bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm" style="min-height: 300px;">
                    <template v-if="spot.mapX && spot.mapY">
                        <!-- OpenStreetMap iframe (좌표 기반, API 키 불필요) -->
                        <iframe
                            :src="`https://www.openstreetmap.org/export/embed.html?bbox=${+spot.mapX - 0.003},${+spot.mapY - 0.002},${+spot.mapX + 0.003},${+spot.mapY + 0.002}&layer=mapnik&marker=${spot.mapY},${spot.mapX}`"
                            class="w-full"
                            style="height: 300px; border: 0;"
                            loading="lazy"
                            :title="`${spot.title} 지도`"
                        ></iframe>
                        <!-- 카카오맵 바로가기 링크 -->
                        <a
                            v-if="kakaoMapUrl"
                            :href="kakaoMapUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold text-gray-500 hover:text-primary hover:bg-orange-50 transition-colors border-t border-gray-100"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            카카오맵에서 크게 보기
                        </a>
                    </template>
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm font-medium" style="min-height: 300px;">
                        지도 정보 없음
                    </div>
                </div>
            </div>

            <!-- 이용 정보 (있을 때만 표시) -->
            <div v-if="infoItems.length > 0" class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm mb-8">
                <h2 class="text-lg font-black text-gray-900 mb-6">이용 정보</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-5">
                    <div v-for="item in infoItems" :key="item.label" class="flex gap-3 items-start">
                        <!-- 시계 아이콘 -->
                        <template v-if="item.icon === 'clock'">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </template>
                        <!-- 달력 아이콘 -->
                        <template v-else-if="item.icon === 'calendar'">
                            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </template>
                        <!-- 티켓 아이콘 -->
                        <template v-else-if="item.icon === 'ticket'">
                            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                            </div>
                        </template>
                        <!-- 차 아이콘 -->
                        <template v-else-if="item.icon === 'car'">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8M3 11l2-5h14l2 5M3 11v6h2v1a1 1 0 002 0v-1h10v1a1 1 0 002 0v-1h2v-6M3 11h18"/>
                                </svg>
                            </div>
                        </template>
                        <!-- 기타 -->
                        <template v-else>
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </template>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">{{ item.label }}</p>
                            <p class="text-gray-800 font-semibold text-sm leading-snug">{{ item.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 소개글 -->
            <div v-if="cleanOverview" class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm mb-8">
                <h2 class="text-lg font-black text-gray-900 mb-4">소개</h2>
                <p class="text-gray-700 leading-relaxed font-medium text-sm whitespace-pre-line">{{ cleanOverview }}</p>
            </div>

            <!-- 추가 이미지 갤러리 -->
            <div v-if="allImages.length > 1" class="mb-12">
                <h2 class="text-2xl font-black text-gray-900 mb-5">사진</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <button
                        v-for="(img, i) in allImages"
                        :key="i"
                        @click="openLightbox(i)"
                        class="relative overflow-hidden rounded-2xl group border-2 border-transparent transition-all"
                        style="aspect-ratio: 4/3;"
                    >
                        <img :src="img" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>

            <!-- 근처 맛집 -->
            <div v-if="nearbyRestaurants.length > 0" class="mb-12">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-black text-gray-900">🍽️ 근처 맛집</h2>
                    <Link :href="route('restaurants.index')" class="text-sm font-bold text-primary hover:underline">전체보기</Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <Link
                        v-for="r in nearbyRestaurants"
                        :key="r.contentId"
                        :href="route('restaurants.show', r.contentId)"
                        class="group relative overflow-hidden rounded-2xl bg-gray-200 border border-black/5 transition-all hover:shadow-lg hover:-translate-y-1"
                        style="min-height: 180px;"
                    >
                        <img
                            v-if="r.image"
                            :src="r.image"
                            :alt="r.title"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 bg-gradient-to-br from-orange-600 to-red-800"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 z-10">
                            <p class="text-[10px] font-black text-primary/90 uppercase tracking-widest mb-1">{{ r.category }}</p>
                            <h3 class="text-white font-black text-base leading-snug">{{ r.title }}</h3>
                            <p v-if="r.address" class="text-white/60 text-xs mt-1 flex items-center gap-1 line-clamp-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ r.address }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- 다른 관광지 -->
            <div v-if="relatedSpots.length > 0">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-black text-gray-900">다른 관광지 둘러보기</h2>
                    <Link :href="route('tour.index')" class="text-sm font-bold text-primary hover:underline">전체보기</Link>
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
                        class="px-8 py-3.5 border-2 border-primary text-primary font-black rounded-full hover:bg-primary hover:text-white transition-all text-sm"
                    >
                        목록으로 돌아가기
                    </Link>
                </div>
            </div>
        </main>

        <AppFooter />
        <ToastNotification />

        <!-- ── 라이트박스 ───────────────────────────────────────── -->
        <Teleport to="body">
            <Transition name="lightbox">
                <div
                    v-if="lightboxOpen"
                    class="fixed inset-0 z-[9999] bg-black/95 flex flex-col"
                    @click.self="closeLightbox"
                >
                    <!-- 상단 바 -->
                    <div class="flex items-center justify-between px-5 py-4 flex-shrink-0">
                        <span class="text-white/60 text-sm font-bold">
                            {{ lightboxIndex + 1 }} / {{ allImages.length }}
                        </span>
                        <button
                            @click="closeLightbox"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors"
                        >
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- 메인 이미지 영역 -->
                    <div class="relative flex-1 flex items-center justify-center px-14 min-h-0">
                        <!-- 이전 버튼 -->
                        <button
                            v-if="allImages.length > 1"
                            @click="prevImage"
                            class="absolute left-3 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors z-10"
                        >
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <Transition name="fade" mode="out-in">
                            <img
                                :key="lightboxIndex"
                                :src="allImages[lightboxIndex]"
                                :alt="`${spot.title} 사진 ${lightboxIndex + 1}`"
                                class="max-w-full max-h-full object-contain select-none rounded-lg shadow-2xl"
                                style="max-height: calc(100vh - 180px);"
                            />
                        </Transition>

                        <!-- 다음 버튼 -->
                        <button
                            v-if="allImages.length > 1"
                            @click="nextImage"
                            class="absolute right-3 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors z-10"
                        >
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- 하단 썸네일 스트립 -->
                    <div v-if="allImages.length > 1" class="flex gap-2 justify-center px-4 py-4 overflow-x-auto flex-shrink-0">
                        <button
                            v-for="(img, i) in allImages"
                            :key="i"
                            @click="lightboxIndex = i"
                            class="flex-shrink-0 w-14 h-10 rounded-lg overflow-hidden border-2 transition-all"
                            :class="lightboxIndex === i ? 'border-primary opacity-100' : 'border-transparent opacity-40 hover:opacity-75'"
                        >
                            <img :src="img" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.lightbox-enter-active,
.lightbox-leave-active { transition: opacity 0.2s ease; }
.lightbox-enter-from,
.lightbox-leave-to    { opacity: 0; }

.fade-enter-active,
.fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from,
.fade-leave-to    { opacity: 0; }
</style>
