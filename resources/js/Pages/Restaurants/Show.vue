<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import LazyImage from '@/Components/LazyImage.vue';

const props = defineProps({
    restaurant:         { type: Object, required: true },
    relatedRestaurants: { type: Array,  default: () => [] },
    nearbySpots:        { type: Array,  default: () => [] },
});

// ── 이미지 갤러리 ────────────────────────────────────────────────
const activeImage = ref(props.restaurant.image || null);

const allImages = computed(() => {
    const imgs = [];
    if (props.restaurant.image) imgs.push(props.restaurant.image);
    (props.restaurant.extraImages ?? []).forEach(url => {
        if (url && !imgs.includes(url)) imgs.push(url);
    });
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

// ── 카카오맵 ─────────────────────────────────────────────────────
const kakaoMapUrl = computed(() => {
    const { mapX, mapY, title } = props.restaurant;
    if (!mapX || !mapY) return null;
    return `https://map.kakao.com/link/map/${encodeURIComponent(title)},${mapY},${mapX}`;
});

const kakaoDirectionUrl = computed(() => {
    const { mapX, mapY, title } = props.restaurant;
    if (!mapX || !mapY) return null;
    return `https://map.kakao.com/link/to/${encodeURIComponent(title)},${mapY},${mapX}`;
});

// ── HTML 태그 제거 ─────────────────────────────────────────────
const stripHtml = (str) => str ? str.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim() : '';

const cleanOverview = computed(() => stripHtml(props.restaurant.overview ?? ''));

// OG / SEO
const pageUrl = computed(() => {
    try { return route('restaurants.show', props.restaurant.content_id); }
    catch { return typeof window !== 'undefined' ? window.location.href : ''; }
});

const ogDescription = computed(() => {
    if (cleanOverview.value) return cleanOverview.value.slice(0, 160);
    return [props.restaurant.category, props.restaurant.address].filter(Boolean).join(' · ') || '대구 맛집 정보';
});

const ogImage = computed(() => props.restaurant.image || null);

// ── 카테고리 스타일 ─────────────────────────────────────────────
const categoryStyle = (cat) => {
    const map = {
        '한식':      'bg-red-100 text-red-700',
        '양식':      'bg-yellow-100 text-yellow-700',
        '일식':      'bg-pink-100 text-pink-700',
        '중식':      'bg-orange-100 text-orange-700',
        '카페':      'bg-amber-100 text-amber-700',
        '이색음식점': 'bg-purple-100 text-purple-700',
        '기타':      'bg-gray-100 text-gray-600',
    };
    return map[cat] || 'bg-gray-100 text-gray-600';
};

const fallbackGradient = (cat) => {
    const map = {
        '한식':      'from-red-700 to-rose-900',
        '양식':      'from-yellow-600 to-orange-700',
        '일식':      'from-pink-700 to-rose-800',
        '중식':      'from-orange-700 to-red-800',
        '카페':      'from-amber-600 to-yellow-800',
        '이색음식점': 'from-purple-700 to-indigo-900',
        '기타':      'from-gray-600 to-gray-800',
    };
    return map[cat] || 'from-gray-600 to-gray-800';
};

const categoryEmoji = (cat) => {
    const map = {
        '한식':      '🍚',
        '양식':      '🍝',
        '일식':      '🍣',
        '중식':      '🥟',
        '카페':      '☕',
        '이색음식점': '🌮',
        '기타':      '🍽️',
    };
    return map[cat] || '🍽️';
};

// ── 이용 정보 항목 ───────────────────────────────────────────────
const infoItems = computed(() => {
    const r = props.restaurant;
    return [
        { icon: 'menu',    label: '대표메뉴', value: stripHtml(r.firstmenu) },
        { icon: 'clock',   label: '영업시간', value: stripHtml(r.opentimefood) },
        { icon: 'calendar',label: '쉬는날',   value: stripHtml(r.restdatefood) },
        { icon: 'car',     label: '주차',     value: stripHtml(r.parkingfood) },
        { icon: 'seat',    label: '좌석수',   value: stripHtml(r.seat) },
        { icon: 'card',    label: '신용카드', value: stripHtml(r.chkcreditcardfood) },
        { icon: 'smoke',   label: '금연여부', value: stripHtml(r.smoking) },
    ].filter(i => i.value);
});
</script>

<template>
    <Head>
        <title>{{ `${restaurant.title} - 대구 맛집 | ttip` }}</title>
        <meta head-key="description" name="description" :content="ogDescription" />

        <!-- Open Graph -->
        <meta head-key="og:type"        property="og:type"        content="website" />
        <meta head-key="og:site_name"   property="og:site_name"   content="ttip" />
        <meta head-key="og:title"       property="og:title"       :content="`${restaurant.title} - 대구 맛집 | ttip`" />
        <meta head-key="og:description" property="og:description" :content="ogDescription" />
        <meta head-key="og:url"         property="og:url"         :content="pageUrl" />
        <meta v-if="ogImage" head-key="og:image" property="og:image" :content="ogImage" />

        <!-- Twitter Card -->
        <meta head-key="twitter:card"        name="twitter:card"        :content="ogImage ? 'summary_large_image' : 'summary'" />
        <meta head-key="twitter:title"       name="twitter:title"       :content="`${restaurant.title} - 대구 맛집 | ttip`" />
        <meta head-key="twitter:description" name="twitter:description" :content="ogDescription" />
        <meta v-if="ogImage" head-key="twitter:image" name="twitter:image" :content="ogImage" />

        <!-- Canonical -->
        <link head-key="canonical" rel="canonical" :href="pageUrl" />
    </Head>

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <!-- ── 히어로 이미지 ── -->
        <div
            class="relative w-full overflow-hidden bg-black"
            style="height: 55vh; min-height: 340px;"
            :class="allImages.length ? 'cursor-zoom-in' : ''"
            @click="allImages.length && openLightbox(allImages.indexOf(activeImage) >= 0 ? allImages.indexOf(activeImage) : 0)"
        >
            <!-- 블러 배경 -->
            <img
                v-if="activeImage"
                :src="activeImage"
                aria-hidden="true"
                class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-50 transition-all duration-500"
            />
            <!-- 메인 이미지 -->
            <img
                v-if="activeImage"
                :src="activeImage"
                :alt="restaurant.title"
                class="absolute inset-0 w-full h-full object-contain transition-all duration-500 pointer-events-none"
            />
            <!-- 이미지 없으면 그라데이션 -->
            <div v-else class="absolute inset-0 bg-gradient-to-br" :class="fallbackGradient(restaurant.category)">
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-9xl opacity-30 select-none">{{ categoryEmoji(restaurant.category) }}</span>
                </div>
            </div>

            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-black/10"></div>

            <!-- 뒤로가기 -->
            <div class="absolute top-6 left-6 z-10" @click.stop>
                <Link
                    :href="route('restaurants.index')"
                    class="inline-flex items-center gap-2 text-white/90 hover:text-white font-bold text-sm bg-black/25 backdrop-blur-sm px-4 py-2 rounded-full transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    대구 맛집
                </Link>
            </div>

            <!-- 타이틀 오버레이 -->
            <div class="absolute bottom-0 left-0 right-0 px-8 pb-8 z-10">
                <span class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-black px-3 py-1 rounded-full mb-3">
                    {{ restaurant.category }}
                </span>
                <h1 class="text-3xl md:text-5xl font-black text-white leading-tight drop-shadow-lg mb-2">
                    {{ restaurant.title }}
                </h1>
                <p v-if="restaurant.address" class="text-white/80 font-medium flex items-center gap-1.5 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ restaurant.address }}
                </p>
            </div>
        </div>

        <!-- 이미지 썸네일 스트립 -->
        <div v-if="allImages.length > 1" class="bg-black flex gap-1.5 px-4 py-2 overflow-x-auto">
            <button
                v-for="(img, i) in allImages"
                :key="i"
                @click="activeImage = img"
                class="flex-shrink-0 w-16 h-12 rounded-lg overflow-hidden border-2 transition-all"
                :class="activeImage === img ? 'border-primary' : 'border-transparent opacity-60 hover:opacity-100'"
            >
                <LazyImage :src="img" class="w-full h-full object-cover" />
            </button>
        </div>

        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-14">

            <!-- 기본 정보 + 지도 -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

                <!-- 좌측: 기본 정보 -->
                <div class="lg:col-span-3 bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-lg font-black text-gray-900 mb-5">기본 정보</h2>

                    <div class="space-y-4">
                        <!-- 주소 -->
                        <div v-if="restaurant.address" class="flex gap-4 items-start">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">주소</p>
                                <p class="text-gray-800 font-semibold text-sm">{{ restaurant.address }}</p>
                            </div>
                        </div>

                        <!-- 전화번호 -->
                        <div v-if="restaurant.tel" class="flex gap-4 items-start">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">연락처</p>
                                <a :href="`tel:${restaurant.tel}`" class="text-gray-800 font-semibold text-sm hover:text-primary">{{ restaurant.tel }}</a>
                            </div>
                        </div>

                        <!-- 카테고리 -->
                        <div class="flex gap-4 items-start">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">분류</p>
                                <p class="text-gray-800 font-semibold text-sm">{{ restaurant.category }}</p>
                            </div>
                        </div>

                        <!-- 홈페이지 -->
                        <div v-if="restaurant.homepage" class="flex gap-4 items-start">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-0.5">홈페이지</p>
                                <a :href="restaurant.homepage" target="_blank" rel="noopener noreferrer" class="text-primary font-semibold text-sm hover:underline">바로가기 →</a>
                            </div>
                        </div>
                    </div>

                    <!-- 카카오맵 길찾기 버튼 -->
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
                    <template v-if="restaurant.mapX && restaurant.mapY">
                        <iframe
                            :src="`https://www.openstreetmap.org/export/embed.html?bbox=${+restaurant.mapX - 0.003},${+restaurant.mapY - 0.002},${+restaurant.mapX + 0.003},${+restaurant.mapY + 0.002}&layer=mapnik&marker=${restaurant.mapY},${restaurant.mapX}`"
                            class="w-full"
                            style="height: 300px; border: 0;"
                            loading="lazy"
                            :title="`${restaurant.title} 지도`"
                        ></iframe>
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

            <!-- 이용 정보 -->
            <div v-if="infoItems.length > 0" class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm mb-8">
                <h2 class="text-lg font-black text-gray-900 mb-6">이용 정보</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-5">
                    <div v-for="item in infoItems" :key="item.label" class="flex gap-3 items-start">
                        <!-- 메뉴 -->
                        <template v-if="item.icon === 'menu'">
                            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </template>
                        <!-- 영업시간 -->
                        <template v-else-if="item.icon === 'clock'">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </template>
                        <!-- 쉬는날 -->
                        <template v-else-if="item.icon === 'calendar'">
                            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </template>
                        <!-- 주차 -->
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
                            <p class="text-gray-800 font-semibold text-sm leading-snug whitespace-pre-line">{{ item.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 소개 -->
            <div v-if="cleanOverview" class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm mb-8">
                <h2 class="text-lg font-black text-gray-900 mb-4">소개</h2>
                <p class="text-gray-700 leading-relaxed font-medium text-sm whitespace-pre-line">{{ cleanOverview }}</p>
            </div>

            <!-- 사진 갤러리 -->
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
                        <LazyImage :src="img" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>

            <!-- 근처 관광지 -->
            <div v-if="nearbySpots.length > 0" class="mb-12">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-black text-gray-900">🗺️ 근처 관광지</h2>
                    <Link :href="route('tour.index')" class="text-sm font-bold text-primary hover:underline">전체보기</Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <Link
                        v-for="s in nearbySpots"
                        :key="s.contentId"
                        :href="route('tour.show', s.contentId)"
                        class="group relative overflow-hidden rounded-2xl bg-gray-200 border border-black/5 transition-all hover:shadow-lg hover:-translate-y-1"
                        style="min-height: 180px;"
                    >
                        <LazyImage
                            v-if="s.image"
                            :src="s.image"
                            :alt="s.title"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 bg-gradient-to-br from-teal-600 to-emerald-800"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 z-10">
                            <p class="text-[10px] font-black text-primary/90 uppercase tracking-widest mb-1">관광지</p>
                            <h3 class="text-white font-black text-base leading-snug">{{ s.title }}</h3>
                            <p v-if="s.addr1" class="text-white/60 text-xs mt-1 flex items-center gap-1 line-clamp-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ s.addr1 }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- 비슷한 맛집 -->
            <div v-if="relatedRestaurants.length > 0">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-2xl font-black text-gray-900">비슷한 {{ restaurant.category }} 맛집</h2>
                    <Link :href="route('restaurants.index')" class="text-sm font-bold text-primary hover:underline">전체보기</Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                    <Link
                        v-for="r in relatedRestaurants"
                        :key="r.contentId"
                        :href="route('restaurants.show', r.contentId)"
                        class="group relative overflow-hidden rounded-2xl bg-gray-200 border border-black/5 transition-all hover:shadow-lg hover:-translate-y-1"
                        style="min-height: 200px;"
                    >
                        <LazyImage
                            v-if="r.image"
                            :src="r.image"
                            :alt="r.title"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div v-else class="absolute inset-0 bg-gradient-to-br" :class="fallbackGradient(r.category)">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-5xl opacity-30 select-none">{{ categoryEmoji(r.category) }}</span>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 z-10">
                            <p class="text-[10px] font-black text-primary/90 uppercase tracking-widest mb-1">{{ r.category }}</p>
                            <h3 class="text-white font-black text-base leading-snug">{{ r.title }}</h3>
                            <p v-if="r.address" class="text-white/60 text-xs mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ r.address }}
                            </p>
                        </div>
                    </Link>
                </div>

                <div class="flex justify-center">
                    <Link
                        :href="route('restaurants.index')"
                        class="px-8 py-3.5 border-2 border-primary text-primary font-black rounded-full hover:bg-primary hover:text-white transition-all text-sm"
                    >
                        목록으로 돌아가기
                    </Link>
                </div>
            </div>
        </main>

        <AppFooter />
        <ToastNotification />

        <!-- ── 라이트박스 ── -->
        <Teleport to="body">
            <Transition name="lightbox">
                <div
                    v-if="lightboxOpen"
                    class="fixed inset-0 z-[9999] bg-black/95 flex flex-col"
                    @click.self="closeLightbox"
                >
                    <div class="flex items-center justify-between px-5 py-4 flex-shrink-0">
                        <span class="text-white/60 text-sm font-bold">{{ lightboxIndex + 1 }} / {{ allImages.length }}</span>
                        <button @click="closeLightbox" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="relative flex-1 flex items-center justify-center px-14 min-h-0">
                        <button v-if="allImages.length > 1" @click="prevImage" class="absolute left-3 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors z-10">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <Transition name="fade" mode="out-in">
                            <img
                                :key="lightboxIndex"
                                :src="allImages[lightboxIndex]"
                                :alt="`${restaurant.title} 사진 ${lightboxIndex + 1}`"
                                class="max-w-full max-h-full object-contain select-none rounded-lg shadow-2xl"
                                style="max-height: calc(100vh - 180px);"
                            />
                        </Transition>

                        <button v-if="allImages.length > 1" @click="nextImage" class="absolute right-3 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center transition-colors z-10">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

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
