<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head, Link } from '@inertiajs/vue3';

import { ref, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  posts: Object,
  categories: Array,
  pinnedNotices: Array,
  currentCategory: String,
  filters: Object,
});

const searchType = ref(props.filters?.search_type || 'title');
const searchKeyword = ref(props.filters?.search_keyword || '');

const executeSearch = () => {
    let queryParams = {};
    if (props.currentCategory && props.currentCategory !== 'all') {
        queryParams.category = props.currentCategory;
    }
    if (searchKeyword.value.trim()) {
        queryParams.search_type = searchType.value;
        queryParams.search_keyword = searchKeyword.value.trim();
    }
    
    router.get(route('home'), queryParams, {
        preserveState: true,
        replace: true,
    });
};

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

      <!-- Search Area -->
      <div class="bg-white p-3 sm:p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row gap-3">
          <select 
             v-model="searchType" 
             class="border-gray-200 text-gray-700 font-medium rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-40 text-sm"
          >
              <option value="title">제목</option>
              <option value="tags">해시태그</option>
              <option value="author">작성자</option>
          </select>
          <div class="flex-1 relative">
              <input 
                 v-model="searchKeyword" 
                 @keydown.enter="executeSearch"
                 type="text" 
                 placeholder="검색어를 입력하세요..." 
                 class="w-full border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-12 text-sm"
              />
              <button @click="executeSearch" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 bg-indigo-50 text-indigo-600 rounded-md hover:bg-indigo-600 hover:text-white transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </button>
          </div>
      </div>

      <!-- Pinned Notices -->
      <div v-if="pinnedNotices && pinnedNotices.length > 0 && currentCategory === 'all'" class="space-y-4">
          <div class="flex items-center space-x-2 px-1">
              <span class="text-lg font-bold text-gray-900">🔔 중요 공지사항</span>
              <div class="h-0.5 flex-1 bg-gray-100 rounded-full"></div>
          </div>
          <div class="grid gap-4">
              <SummaryCard
                v-for="notice in pinnedNotices"
                :key="'pinned-' + notice.id"
                :id="notice.id"
                :author-name="notice.authorName"
                :author-avatar="notice.authorAvatar"
                :time-ago="notice.timeAgo"
                :category="notice.category"
                :tags="notice.tags"
                :title="notice.title"
                :summary="notice.summary"
                :likes="notice.likes"
                :views="notice.views"
                :type="'notice'"
                :is-pinned="true"
                class="border-l-4 border-l-red-500"
              />
          </div>
      </div>

      <div v-if="postList && postList.length > 0" class="flex flex-col gap-6">
          <div v-if="currentCategory === 'all' && pinnedNotices && pinnedNotices.length > 0" class="flex items-center space-x-2 px-1 mt-4">
              <span class="text-sm font-bold text-gray-400">전체 피드</span>
              <div class="h-0.5 flex-1 bg-gray-50 rounded-full"></div>
          </div>
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
            :views="post.views"
            :type="post.type"
            :is-pinned="post.isPinned"
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
