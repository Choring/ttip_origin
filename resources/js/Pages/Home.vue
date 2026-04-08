<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head, Link } from '@inertiajs/vue3';

import { ref, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  posts: Object,
  categories: Array,
  currentCategory: String
});

const postList = ref(props.posts.data);
const nextPageUrl = ref(props.posts.next_page_url);
const isLoading = ref(false);
const observerTarget = ref(null);
let observer = null;

const loadMorePosts = async () => {
    if (isLoading.value || !nextPageUrl.value) return;
    
    isLoading.value = true;
    try {
        const response = await axios.get(nextPageUrl.value);
        postList.value = [...postList.value, ...response.data.data];
        nextPageUrl.value = response.data.next_page_url;
    } catch (error) {
        console.error('Failed to load posts', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMorePosts();
        }
    }, {
        rootMargin: '0px 0px 300px 0px'
    });

    if (observerTarget.value) {
        observer.observe(observerTarget.value);
    }
});

onUnmounted(() => {
    if (observer) observer.disconnect();
});

watch(() => props.posts, (newPosts) => {
    postList.value = newPosts.data;
    nextPageUrl.value = newPosts.next_page_url;
}, { deep: true });
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

      <div v-if="postList && postList.length > 0" class="flex flex-col gap-6">
          <SummaryCard
            v-for="post in postList"
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
      </div>

      <!-- 무한 스크롤 옵저버 타겟 -->
      <div ref="observerTarget" class="h-10 w-full" v-show="nextPageUrl">
        <div v-if="isLoading" class="flex justify-center items-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>
      </div>

      <div v-if="!postList || postList.length === 0" class="text-center py-16 text-gray-500 bg-white border border-gray-200 rounded-3xl shadow-sm">
        해당 카테고리에 작성된 게시글이 아직 없습니다.
      </div>
    </div>
  </MainLayout>
</template>
