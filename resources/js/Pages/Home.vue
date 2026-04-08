<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  posts: Array,
  categories: Array,
  currentCategory: String
});
</script>

<template>
  <Head title="ttip - 탐색" />

  <MainLayout>
    <div class="space-y-6">
      
      <!-- Category Tabs (Horizontal Scroll / Wrap) - Mobile Only -->
      <div v-if="categories && categories.length > 0" class="md:hidden flex flex-wrap gap-2 pb-2">
        <Link 
            :href="route('home')"
            class="px-4 py-2 rounded-full text-sm font-bold transition-all"
            :class="currentCategory === 'all' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
        >
            전체 피드
        </Link>
        <Link 
            v-for="cat in categories" 
            :key="cat.id" 
            :href="route('home', { category: cat.slug })"
            class="px-4 py-2 rounded-full text-sm font-bold transition-all"
            :class="currentCategory === cat.slug ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
        >
            {{ cat.name }}
        </Link>
      </div>

      <div>
          <SummaryCard
            v-for="post in posts"
            :key="post.id"
            :id="post.id"
            :author-name="post.authorName"
            :author-avatar="post.authorAvatar"
            :time-ago="post.timeAgo"
            :category="post.category"
            :tag="post.tag"
            :title="post.title"
            :summary="post.summary"
            :likes="post.likes"
          />
      </div>

      <div v-if="!posts || posts.length === 0" class="text-center py-16 text-gray-500 bg-white border border-gray-200 rounded-3xl shadow-sm">
        해당 카테고리에 작성된 게시글이 아직 없습니다.
      </div>
    </div>
  </MainLayout>
</template>
