<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: Number,
});

const errorInfo = computed(() => ({
    503: {
        code:        '503',
        title:       '점검 중입니다',
        description: '더 나은 서비스를 위해 잠시 시스템 점검 중입니다.\n잠시 후 다시 시도해주세요.',
        emoji:       '🚧',
        color:       'from-amber-400 to-orange-500',
        showBack:    false,
    },
    500: {
        code:        '500',
        title:       '서버 오류가 발생했습니다',
        description: '예상치 못한 문제가 발생했습니다.\n잠시 후 다시 시도하거나 고객지원으로 문의해주세요.',
        emoji:       '💥',
        color:       'from-red-400 to-rose-600',
        showBack:    false,
    },
    404: {
        code:        '404',
        title:       '페이지를 찾을 수 없습니다',
        description: '요청하신 페이지가 삭제되었거나\n주소가 올바르지 않습니다.',
        emoji:       '🔍',
        color:       'from-indigo-400 to-purple-500',
        showBack:    true,
    },
    403: {
        code:        '403',
        title:       '접근 권한이 없습니다',
        description: '이 페이지를 볼 수 있는 권한이 없습니다.\n로그인 후 다시 시도해주세요.',
        emoji:       '🚫',
        color:       'from-gray-400 to-gray-600',
        showBack:    true,
    },
    429: {
        code:        '429',
        title:       '잠시 멈춰주세요',
        description: '너무 많은 요청이 감지되었습니다.\n잠시 후 다시 시도해주세요.',
        emoji:       '⏳',
        color:       'from-yellow-400 to-amber-500',
        showBack:    false,
    },
}[props.status] ?? {
    code:        String(props.status ?? ''),
    title:       '오류가 발생했습니다',
    description: '서비스 이용에 불편을 드려 죄송합니다.',
    emoji:       '⚠️',
    color:       'from-gray-400 to-gray-600',
    showBack:    true,
}));
</script>

<template>
    <Head :title="`${errorInfo.code} - ${errorInfo.title} | ttip`" />

    <!-- 완전 독립 레이아웃: 공유 props 불필요 -->
    <div class="min-h-screen bg-gray-50 flex flex-col font-sans">

        <!-- 미니 헤더 -->
        <header class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-6 h-14 flex items-center">
                <a href="/" class="flex items-center gap-2 group">
                    <span class="text-xl font-black text-primary tracking-tight group-hover:opacity-80 transition-opacity">
                        ttip
                    </span>
                    <span class="text-xs text-gray-400 font-medium hidden sm:inline">대구 사람들의 꿀팁</span>
                </a>
            </div>
        </header>

        <!-- 에러 본문 -->
        <main class="flex-1 flex items-center justify-center px-4 py-16">
            <div class="w-full max-w-md text-center">

                <!-- 상태 코드 배지 -->
                <div class="inline-flex items-center justify-center mb-8">
                    <div
                        class="w-24 h-24 rounded-3xl bg-gradient-to-br flex items-center justify-center shadow-xl"
                        :class="errorInfo.color"
                    >
                        <span class="text-4xl select-none">{{ errorInfo.emoji }}</span>
                    </div>
                </div>

                <!-- 코드 + 제목 -->
                <p class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-2">
                    ERROR {{ errorInfo.code }}
                </p>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-4 leading-tight">
                    {{ errorInfo.title }}
                </h1>
                <p class="text-gray-500 leading-relaxed whitespace-pre-line text-sm md:text-base mb-10">
                    {{ errorInfo.description }}
                </p>

                <!-- 버튼 -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a
                        href="/"
                        class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-primary text-white font-black rounded-2xl shadow-lg hover:bg-[#E65300] hover:-translate-y-0.5 transition-all active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        홈으로 돌아가기
                    </a>

                    <button
                        v-if="errorInfo.showBack"
                        type="button"
                        @click="() => window.history.back()"
                        class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-gray-700 font-bold rounded-2xl border border-gray-200 hover:bg-gray-50 transition-all active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                        이전 페이지로
                    </button>
                </div>

                <!-- 도움말 -->
                <p class="mt-12 text-xs text-gray-400">
                    문제가 계속된다면
                    <a href="/inquiry" class="text-primary font-bold hover:underline">고객지원</a>
                    으로 문의해주세요.
                </p>
            </div>
        </main>

        <!-- 미니 푸터 -->
        <footer class="py-5 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">
                © {{ new Date().getFullYear() }} ttip. All rights reserved.
            </p>
        </footer>
    </div>
</template>
