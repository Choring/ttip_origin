<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import PopularPostsWidget from '@/Components/PopularPostsWidget.vue';
import HallOfFameWidget from '@/Components/HallOfFameWidget.vue';
import PartnerSitesWidget from '@/Components/PartnerSitesWidget.vue';
import LoginModal from '@/Components/LoginModal.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import PlaceCard from '@/Components/Categories/PlaceCard.vue';
import JobCard from '@/Components/Categories/JobCard.vue';
import GymCard from '@/Components/Categories/GymCard.vue';

const showLoginModal = ref(false);

const props = defineProps({
  posts:           Object,
  categories:      Array,
  pinnedNotices:   Array,
  currentCategory: String,
  filters:         Object,
});

const searchType    = ref(props.filters?.search_type || 'title');
const searchKeyword = ref(props.filters?.search_keyword || '');
const isSearchVisible = ref(!!props.filters?.search_keyword);

const searchPlaceholder = computed(() => {
    const map = {
        title:         '제목으로 검색...',
        title_content: '제목 또는 본문 내용으로 검색...',
        content:       '본문 내용으로 검색...',
        tags:          '#태그명으로 검색...',
        author:        '작성자 닉네임으로 검색...',
    };
    return map[searchType.value] ?? '검색어를 입력하세요...';
});

const toggleSearch = () => { isSearchVisible.value = !isSearchVisible.value; };

const executeSearch = () => {
    const queryParams = {};
    if (props.currentCategory && props.currentCategory !== 'all') {
        queryParams.category = props.currentCategory;
    }
    if (searchKeyword.value.trim()) {
        queryParams.search_type    = searchType.value;
        queryParams.search_keyword = searchKeyword.value.trim();
    }
    router.get(route('community'), queryParams, { preserveState: true, replace: true });
};

const postList    = ref(props.posts.data);
const nextPageUrl = ref(props.posts.next_page_url);
const isLoading   = ref(false);
const observerTarget = ref(null);
let observer = null;

const loadMorePosts = async () => {
    if (isLoading.value || !nextPageUrl.value) return;
    isLoading.value = true;
    try {
        const response = await axios.get(nextPageUrl.value);
        postList.value    = [...postList.value, ...response.data.data];
        nextPageUrl.value = response.data.next_page_url;
    } catch (error) {
        console.error('Failed to load posts', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) loadMorePosts();
    }, { rootMargin: '0px 0px 300px 0px' });
    if (observerTarget.value) observer.observe(observerTarget.value);
});

onUnmounted(() => { if (observer) observer.disconnect(); });

watch(() => props.posts, (newPosts) => {
    postList.value    = newPosts.data;
    nextPageUrl.value = newPosts.next_page_url;
}, { deep: true });

const containerClass = computed(() => {
    if (props.currentCategory === 'all') return 'flex flex-col gap-6';
    if (['restaurant', 'cafe', 'solo-dining'].includes(props.currentCategory)) return 'grid grid-cols-2 md:grid-cols-3 gap-4';
    return 'flex flex-col gap-3';
});

const getCardComponent = (post) => {
    if (props.currentCategory === 'all') return SummaryCard;
    const map = {
        'restaurant':  PlaceCard,
        'cafe':        PlaceCard,
        'solo-dining': PlaceCard,
        'part-time':   JobCard,
        'gym':         GymCard,
    };
    return map[props.currentCategory] || SummaryCard;
};
</script>

<template>
  <Head>
    <title>커뮤니티 - ttip</title>
    <meta head-key="description" name="description" content="대구 시민들의 생생한 정보와 이야기. ttip 커뮤니티에서 소통하세요.">
  </Head>

  <MainLayout>
    <div class="space-y-6">

      <!-- 카테고리 탭 (모바일) -->
      <div v-if="categories && categories.length > 0" class="md:hidden -mx-4 px-4 relative">
        <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none z-10"></div>
        <div class="flex overflow-x-auto gap-2 pb-1 no-scrollbar scroll-smooth">
          <Link :href="route('community')"
            class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all"
            :class="currentCategory === 'all' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200'"
          >전체</Link>
          <Link v-for="cat in categories.filter(c => c.slug !== 'notice')" :key="cat.id"
            :href="route('community', { category: cat.slug })"
            class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all"
            :class="currentCategory === cat.slug ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200'"
          >{{ cat.name }}</Link>
          <div class="flex-shrink-0 w-8"></div>
        </div>
      </div>

      <!-- 검색창 (데스크탑) -->
      <div class="hidden md:flex bg-white p-4 rounded-xl shadow-sm border border-gray-200 gap-3">
        <select v-model="searchType" class="border-gray-200 text-gray-700 font-medium rounded-lg shadow-sm focus:border-indigo-500 w-40 text-sm">
          <option value="title">제목</option>
          <option value="title_content">제목+본문</option>
          <option value="content">본문</option>
          <option value="tags">해시태그</option>
          <option value="author">작성자</option>
        </select>
        <div class="flex-1 relative">
          <input v-model="searchKeyword" @keydown.enter="executeSearch" type="text"
            :placeholder="searchPlaceholder"
            class="w-full border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 pr-12 text-sm"
          />
          <button @click="executeSearch" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 bg-indigo-50 text-indigo-600 rounded-md hover:bg-indigo-600 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </button>
        </div>
      </div>

      <!-- 검색창 (모바일) -->
      <div class="md:hidden space-y-3">
        <div class="flex items-center justify-between px-1">
          <button @click="toggleSearch"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl border transition-all shadow-sm"
            :class="isSearchVisible ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-200 text-gray-700'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-xs font-black">{{ isSearchVisible ? '검색창 닫기' : '검색하기' }}</span>
          </button>
        </div>
        <div v-show="isSearchVisible" class="bg-white p-3 rounded-xl shadow-sm border border-indigo-100 flex flex-col gap-2">
          <select v-model="searchType" class="border-gray-200 text-gray-700 font-medium rounded-lg shadow-sm flex-1 text-sm py-2">
            <option value="title">제목</option>
            <option value="title_content">제목+본문</option>
            <option value="content">본문</option>
            <option value="tags">태그</option>
            <option value="author">작성자</option>
          </select>
          <div class="relative">
            <input v-model="searchKeyword" @keydown.enter="executeSearch" type="text"
              :placeholder="searchPlaceholder"
              class="w-full border-gray-200 rounded-lg shadow-sm pr-12 text-sm py-2"
            />
            <button @click="executeSearch" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 bg-indigo-600 text-white rounded-md">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- 고정 공지 -->
      <div v-if="pinnedNotices && pinnedNotices.length > 0" class="space-y-4">
        <div class="flex items-center space-x-2 px-1">
          <span class="text-lg font-bold text-gray-900">🔔 중요 공지사항</span>
          <div class="h-0.5 flex-1 bg-gray-100 rounded-full"></div>
        </div>
        <div class="grid gap-4">
          <SummaryCard v-for="notice in pinnedNotices" :key="'pinned-' + notice.id"
            :id="notice.id" :author-name="notice.authorName" :author-avatar="notice.authorAvatar"
            :time-ago="notice.timeAgo" :category="notice.category" :tags="notice.tags"
            :title="notice.title" :summary="notice.summary" :likes="notice.likes"
            :comments="notice.comments" :views="notice.views"
            :author-tier-name="notice.authorTierName" :author-tier-icon="notice.authorTierIcon"
            :type="'notice'" :is-pinned="true" class="border-l-4 border-l-red-500"
          />
        </div>
      </div>

      <!-- 피드 -->
      <div v-if="postList && postList.length > 0" :class="containerClass">
        <div v-if="currentCategory === 'all' && pinnedNotices && pinnedNotices.length > 0"
          class="flex items-center space-x-2 px-1 mt-4 w-full">
          <span class="text-sm font-bold text-gray-400">전체 피드</span>
          <div class="h-0.5 flex-1 bg-gray-50 rounded-full"></div>
        </div>

        <component v-for="post in postList" :key="post.id"
          :is="getCardComponent(post)"
          :id="post.id" :author-name="post.authorName" :author-avatar="post.authorAvatar"
          :time-ago="post.timeAgo" :category="post.category" :category-slug="post.categorySlug"
          :tags="post.tags" :title="post.title" :summary="post.summary"
          :likes="post.likes" :comments="post.comments" :views="post.views"
          :type="post.type" :is-pinned="post.isPinned"
          :author-tier-name="post.authorTierName" :author-tier-icon="post.authorTierIcon"
          :extra_info="post.extra_info" :card_image_path="post.card_image_path"
          :card_image_url="post.card_image_url" :content-excerpt="post.contentExcerpt"
          :is-bookmarked="post.isBookmarked"
        />
      </div>

      <!-- 무한 스크롤 -->
      <div ref="observerTarget" class="h-10 w-full" v-show="nextPageUrl">
        <div v-if="isLoading" class="flex justify-center items-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>
      </div>

      <!-- 빈 상태 -->
      <div v-if="!postList || postList.length === 0" class="text-center py-20 bg-white border border-gray-100 rounded-2xl shadow-sm">
        <div class="text-5xl mb-4">🗂️</div>
        <p class="text-base font-bold text-gray-600 mb-1">아직 게시글이 없어요</p>
        <p class="text-sm text-gray-400 mb-6">첫 번째 글을 작성해 보세요!</p>
        <Link :href="route('posts.create')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-full hover:bg-indigo-700 transition-colors">
          ✏️ 글 쓰러 가기
        </Link>
      </div>
    </div>
  </MainLayout>

  <LoginModal :show="showLoginModal" @close="showLoginModal = false" />
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
