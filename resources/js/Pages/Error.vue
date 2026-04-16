<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
  status: Number,
});

const errorInfo = computed(() => {
  return {
    503: {
      title: '점검 중입니다',
      description: '더 나은 서비스를 위해 잠시 시스템 점검 중입니다. 잠시 후 다시 시도해주세요.',
      emoji: '🚧',
    },
    500: {
      title: '서버 오류가 발생했습니다',
      description: '죄송합니다. 서버 처리 중 예상치 못한 문제가 발생했습니다. 관리자에게 문의해주세요.',
      emoji: '💥',
    },
    404: {
      title: '페이지를 찾을 수 없습니다',
      description: '요청하신 페이지가 삭제되었거나 주소가 올바르지 않습니다.',
      emoji: '📉',
    },
    403: {
      title: '접근 권한이 없습니다',
      description: '이 페이지를 볼 수 있는 권한이 없습니다.',
      emoji: '🚫',
    },
    429: {
        title: '너무 많은 요청입니다',
        description: '잠시 후 다시 시도해주세요.',
        emoji: '⏳',
    }
  }[props.status] || {
    title: '오류가 발생했습니다',
    description: '서비스 이용에 불편을 드려 죄송합니다.',
    emoji: '⚠️',
  };
});
</script>

<template>
  <Head :title="errorInfo.title" />

  <MainLayout>
    <div class="min-h-[60vh] flex flex-col items-center justify-center p-6 text-center">
      <!-- Animated Emoji -->
      <div class="text-8xl mb-8 animate-bounce transition-transform duration-1000">
        {{ errorInfo.emoji }}
      </div>

      <h1 class="text-4xl font-black text-gray-900 mb-4 tracking-tight">
        {{ status }} - {{ errorInfo.title }}
      </h1>

      <p class="text-lg text-gray-500 mb-10 max-w-md leading-relaxed">
        {{ errorInfo.description }}
      </p>

      <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
        <Link 
          :href="route('home')" 
          class="inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all active:scale-95"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          홈으로 돌아가기
        </Link>
        
        <button 
          @click="() => window.history.back()"
          class="inline-flex items-center justify-center px-8 py-3.5 bg-white text-gray-700 font-bold rounded-2xl border border-gray-200 hover:bg-gray-50 transition-all active:scale-95"
        >
          이전 페이지로
        </button>
      </div>

      <!-- Footer Help -->
      <div class="mt-16 pt-8 border-t border-gray-100 w-full max-w-xs">
        <p class="text-xs text-gray-400">
          지속적으로 문제가 발생한다면<br>
          <a href="mailto:support@ttip.com" class="text-indigo-500 font-bold hover:underline">고객지원실</a>로 문의 부탁드립니다.
        </p>
      </div>
    </div>
  </MainLayout>
</template>
