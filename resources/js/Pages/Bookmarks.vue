<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="ttip - 북마크" />

  <MainLayout>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
      <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
      </svg>
      <h3 class="text-lg font-bold text-gray-900 mb-2">저장한 글이 없습니다</h3>
      <p class="text-sm text-gray-500">마음에 드는 글의 북마크 버튼을 눌러 저장해보세요.</p>
    </div>
  </MainLayout>
</template>
