<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import FloatingActionButton from '@/Components/FloatingActionButton.vue';

const props = defineProps({
    events: {
        type: Array,
        default: () => []
    }
});

// 외부 라이브러리 없이 순수 JS로 날짜 포맷팅 (예: 2026-05-12 -> 2026.05.12)
const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return dateStr.substring(0, 10).replace(/-/g, '.');
};

const getEventColor = (gubun) => {
    // 시안 느낌을 살린 그라데이션 및 색상 프리셋
    const presets = {
        '전시': {
            bg: 'from-[#e0f2fe] to-[#ccfbf1]',
            text: 'text-gray-900',
            button: 'bg-black text-white hover:bg-gray-800'
        },
        '공연': {
            bg: 'from-[#6366f1] to-[#312e81]',
            text: 'text-white',
            button: 'bg-white text-indigo-900 hover:bg-gray-100'
        },
        '뮤지컬': {
            bg: 'from-[#475569] to-[#1e293b]',
            text: 'text-white',
            button: 'bg-transparent border border-white text-white hover:bg-white/10'
        },
        '기타': {
            bg: 'from-[#fdf2f8] to-[#fce7f3]',
            text: 'text-gray-900',
            button: 'bg-black text-white hover:bg-gray-800'
        }
    };
    
    // 키워드 기반으로 매칭 (없으면 기타)
    if (gubun && gubun.includes('전시')) return presets['전시'];
    if (gubun && gubun.includes('공연')) return presets['공연'];
    if (gubun && gubun.includes('뮤지컬')) return presets['뮤지컬'];
    
    return presets['기타'];
};

// HTML 엔티티 디코딩 함수 (&lt; -> < 등 변환)
const decodeHtml = (text) => {
    if (!text) return '';
    return text
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&')
        .replace(/&quot;/g, '"')
        .replace(/&#39;/g, "'")
        .replace(/&nbsp;/g, ' ');
};

// 프론트엔드 더보기(Pagination) 로직
const visibleCount = ref(12); // 처음엔 12개 렌더링
const visibleEvents = computed(() => props.events.slice(0, visibleCount.value));

const loadMore = () => {
    visibleCount.value += 12;
};

// 상단으로 가기 (Scroll to Top) 로직
const showScrollTop = ref(false);

const handleScroll = () => {
    // 다양한 브라우저 호환성을 위해 scrollY 또는 scrollTop 사용
    const scrollPosition = window.scrollY || document.documentElement.scrollTop;
    showScrollTop.value = scrollPosition > 150;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // 초기 렌더링 시 강제 확인
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <Head title="대구 문화/행사" />
    
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />
        
        <!-- Full width layout instead of max-w-7xl -->
        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-10 md:py-16">
            <!-- Header Section -->
            <div class="mb-12 flex justify-between items-end">
                <div>
                    <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-4">
                        대구 문화/행사
                    </h1>
                    <p class="text-lg text-gray-500 font-medium">
                        대구광역시에서 진행하는 다양한 전시와 공연 정보를 만나보세요.
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="events.length === 0" class="text-center py-24 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="text-6xl mb-4">🎭</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">현재 진행 중인 행사가 없습니다</h3>
                <p class="text-gray-500">잠시 후 다시 확인해주세요.</p>
            </div>

        <!-- Bento/Masonry Grid (Expanded for Ultra-wide screens) -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 xl:gap-8 auto-rows-min">
                <a 
                    v-for="(event, index) in visibleEvents" 
                    :key="event.event_seq"
                    :href="event.homepage || '#'"
                    :target="event.homepage ? '_blank' : '_self'"
                    class="group relative overflow-hidden rounded-[2rem] bg-gradient-to-br p-8 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 border border-black/5 flex flex-col justify-between"
                    :class="[
                        getEventColor(event.event_gubun).bg,
                        getEventColor(event.event_gubun).text,
                        index % 5 === 0 ? 'md:col-span-2 lg:col-span-2 md:aspect-[2/1]' : 'aspect-square md:aspect-auto md:min-h-[420px]'
                    ]"
                >
                    <!-- Background Glow / Blur Effect -->
                    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: radial-gradient(circle at 50% 50%, white 0%, transparent 80%);"></div>
                    
                    <!-- Vertical Ticket Text (Right Side) -->
                    <div class="absolute right-6 top-0 bottom-0 flex flex-col justify-center opacity-10 tracking-[0.4em] font-black uppercase pointer-events-none text-6xl" style="writing-mode: vertical-rl;">
                        ADMISSION
                    </div>

                    <div class="relative z-10 flex-1">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded text-xs font-black tracking-wider uppercase border border-white/30">
                                {{ event.event_gubun || '문화행사' }}
                            </span>
                            <span v-if="event.pay === '무료'" class="px-3 py-1 bg-green-400/20 text-green-800 rounded text-xs font-black tracking-wider bg-white">
                                FREE
                            </span>
                            <span v-if="index % 5 === 0" class="px-3 py-1 bg-red-500 text-white rounded text-xs font-black tracking-wider flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                LIVE NOW
                            </span>
                        </div>

                        <h2 
                            class="font-black leading-tight mb-3 group-hover:scale-[1.02] transition-transform origin-left drop-shadow-sm"
                            :class="index % 5 === 0 ? 'text-3xl md:text-4xl w-4/5' : 'text-xl md:text-2xl w-full pr-4'"
                        >
                            {{ decodeHtml(event.subject) }}
                        </h2>
                        
                        <!-- Content Preview (only for large cards) -->
                        <p v-if="index % 5 === 0 && event.content" class="w-3/4 opacity-80 font-medium line-clamp-2 mt-4 text-sm">
                            {{ decodeHtml(event.content).replace(/<[^>]*>?/gm, '') }}
                        </p>
                    </div>

                    <div class="relative z-10 mt-auto pt-6 flex justify-between items-end gap-4 w-full">
                        <!-- Left Side: Date (and Venue for large cards) -->
                        <div class="flex flex-col gap-4 min-w-0 flex-1">
                            <!-- Large Card: Flex Row for Date and Venue -->
                            <div v-if="index % 5 === 0" class="flex flex-col sm:flex-row gap-4 sm:gap-8 min-w-0">
                                <div class="shrink-0">
                                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Date</p>
                                    <p class="font-bold text-base md:text-lg drop-shadow-sm">
                                        {{ formatDate(event.start_date) }} <span class="mx-1 text-sm opacity-60">—</span> <span>{{ formatDate(event.end_date) }}</span>
                                    </p>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Venue</p>
                                    <p class="font-bold truncate text-base md:text-lg drop-shadow-sm">{{ event.place }}</p>
                                </div>
                            </div>
                            
                            <!-- Small Card: Just Date here -->
                            <div v-else class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Date</p>
                                <p class="font-bold text-sm md:text-base drop-shadow-sm leading-tight">
                                    {{ formatDate(event.start_date) }}<br/>
                                    <span class="text-xs opacity-60">~</span><br/>
                                    {{ formatDate(event.end_date) }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Right Side: Button (and Venue for small cards) -->
                        <div class="flex flex-col items-end shrink-0 max-w-[55%] min-w-[100px]">
                            <div v-if="index % 5 !== 0" class="mb-3 text-right w-full min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Venue</p>
                                <p class="font-bold truncate text-sm drop-shadow-sm w-full">{{ event.place }}</p>
                            </div>
                            
                            <button 
                                class="px-4 py-2 md:px-5 md:py-2.5 rounded text-[11px] md:text-xs font-bold tracking-widest uppercase transition-all duration-300 whitespace-nowrap shadow-sm"
                                :class="getEventColor(event.event_gubun).button"
                            >
                                예매하기
                            </button>
                            <p class="text-[9px] font-bold uppercase tracking-widest opacity-40 mt-1.5 text-right w-full">{{ event.pay && event.pay !== '무료' ? event.pay : 'KRW 0' }}</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Load More Button -->
            <div v-if="visibleCount < events.length" class="mt-16 flex justify-center">
                <button 
                    @click="loadMore" 
                    class="px-8 py-4 bg-white border border-gray-200 text-gray-900 rounded-full font-bold shadow-sm hover:shadow-md hover:bg-gray-50 transition-all flex items-center gap-2"
                >
                    더보기 ({{ visibleCount }} / {{ events.length }})
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </main>

        <!-- Scroll to Top Button -->
        <button 
            @click="scrollToTop"
            class="fixed bottom-8 right-8 z-50 p-4 rounded-full bg-primary text-white shadow-xl hover:bg-[#E65300] hover:-translate-y-1 transition-all duration-300"
            :class="showScrollTop ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10 pointer-events-none'"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
        </button>

        <AppFooter />
        <ToastNotification />
        <FloatingActionButton />
    </div>
</template>
