<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    posts: Array,
});
</script>

<template>
  <Head title="ttip - 북마크" />

  <MainLayout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-amber-500">🔖</span> 내가 북마크한 팁
            </h1>
            <span class="text-sm text-gray-400 font-medium">총 {{ posts.length }}개</span>
        </div>

        <div v-if="posts && posts.length > 0" class="flex flex-col gap-4">
            <SummaryCard
                v-for="post in posts"
                :key="post.id"
                v-bind="post"
                :is-bookmarked="true"
            />
        </div>

        <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">저장한 글이 없습니다</h3>
            <p class="text-sm text-gray-400">마음에 드는 글의 북마크 버튼을 눌러 저장해보세요.</p>
        </div>
    </div>
  </MainLayout>
</template>

