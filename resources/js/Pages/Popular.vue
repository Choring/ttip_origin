<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  posts: Array,
});
</script>

<template>
  <Head title="ttip - 인기글" />

  <MainLayout>
    <div class="space-y-6">
      <SummaryCard
        v-for="post in posts"
        :key="post.id"
        :id="post.id"
        :author-name="post.authorName"
        :author-avatar="post.authorAvatar"
        :time-ago="post.timeAgo"
        :category="post.category"
        :tags="post.tags"
        :title="post.title"
        :summary="post.summary"
        :likes="post.likes"
      />

      <div v-if="!posts || posts.length === 0" class="text-center py-10 text-gray-500">
        아직 인기 게시물이 없습니다.
      </div>
    </div>
  </MainLayout>
</template>
