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

const showLoginModal = ref(false);

// Category Specialized Cards
import PlaceCard from '@/Components/Categories/PlaceCard.vue';
import JobCard from '@/Components/Categories/JobCard.vue';
import GymCard from '@/Components/Categories/GymCard.vue';

const props = defineProps({
  posts: Object,
  categories: Array,
  pinnedNotices: Array,
  currentCategory: String,
  filters: Object,
});

const searchType = ref(props.filters?.search_type || 'title');
const searchKeyword = ref(props.filters?.search_keyword || '');
const isSearchVisible = ref(!!props.filters?.search_keyword); // 검색어가 있으면 열어둠

const toggleSearch = () => {
    isSearchVisible.value = !isSearchVisible.value;
};

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

// 레이아웃 스위칭 로직
const containerClass = computed(() => {
    if (props.currentCategory === 'all') return 'flex flex-col gap-6';
    
    // 맛집, 카페 카테고리는 그리드로 노출
    if (['restaurant', 'cafe', 'solo-dining'].includes(props.currentCategory)) {
        return 'grid grid-cols-2 md:grid-cols-3 gap-4';
    }
    
    // 그 외(알바 등)는 리스트 형태
    return 'flex flex-col gap-3';
});

const getCardComponent = (post) => {
    // 전체 피드일 때는 기본SummaryCard 사용
    if (props.currentCategory === 'all') return SummaryCard;
    
    // 카테고리별 특화 컴포넌트 매핑
    const componentMap = {
        'restaurant': PlaceCard,
        'cafe': PlaceCard,
        'solo-dining': PlaceCard,
        'part-time': JobCard,
        'gym': GymCard,
    };
    
    return componentMap[props.currentCategory] || SummaryCard;
};

// 현재 페이지의 고유 URL (Canonical URL)
const currentUrl = computed(() => {
    try { 
        return props.currentCategory === 'all' 
            ? route('home') 
            : route('home', { category: props.currentCategory }); 
    }
    catch { 
        return typeof window !== 'undefined' ? window.location.href.split('?')[0] : ''; 
    }
});
</script>

<template>
  <Head>
    <title>{{ currentCategory === 'all' ? 'ttip - 특별한 팁이 가득한 공간' : `ttip - ${currentCategory} 카테고리` }}</title>
    <meta head-key="description" name="description" :content="currentCategory === 'all' ? 'ttip은 당신의 일상에 특별한 팁을 더하는 커뮤니티 공간입니다. 유용한 정보와 즐거운 이야기를 나누어보세요.' : `${currentCategory}에 관한 유용한 정보와 꿀팁들을 ttip에서 확인해보세요.`">
    <meta property="og:title" :content="currentCategory === 'all' ? 'ttip - 특별한 팁이 가득한 공간' : `ttip - ${currentCategory} 탐색`" />
    <link head-key="canonical" rel="canonical" :href="currentUrl" />
  </Head>

  <MainLayout>
    <div class="space-y-6">

      <!-- 웰컴 배너 (전체 피드 + 비로그인 시만 표시) -->
      <div
        v-if="currentCategory === 'all' && !$page.props.auth.user"
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 px-6 py-5 shadow-sm"
      >
        <!-- 배경 장식 원 -->
        <div class="absolute -right-6 -top-6 w-28 h-28 bg-orange-100 rounded-full opacity-60"></div>
        <div class="absolute -right-2 bottom-0 w-16 h-16 bg-amber-100 rounded-full opacity-60"></div>

        <p class="text-xs font-bold uppercase tracking-widest text-orange-400 mb-1">대구 20-30대를 위한</p>
        <h2 class="text-lg font-black leading-snug mb-3 text-orange-900">
          광고 없는 깔끔한<br>대구 생활 정보 커뮤니티 🎯
        </h2>
        <button
          @click="showLoginModal = true"
          class="inline-flex items-center gap-1.5 bg-orange-500 text-white text-xs font-black px-4 py-2 rounded-full shadow-sm hover:bg-orange-600 transition-colors"
        >
          지금 시작하기 →
        </button>
      </div>

      <!-- Category Tabs (Horizontal Scroll) - Mobile Only -->
      <div v-if="categories && categories.length > 0" class="md:hidden -mx-4 px-4 relative">
        <!-- Scroll indicator gradient -->
        <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none z-10"></div>
        
        <div class="flex overflow-x-auto gap-2 pb-1 no-scrollbar scroll-smooth">
          <Link
              :href="route('home')"
              class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all relative z-0"
              :class="currentCategory === 'all' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
          >
              전체 피드
          </Link>
          <Link
              v-for="cat in categories.filter(c => c.slug !== 'notice')"
              :key="cat.id"
              :href="route('home', { category: cat.slug })"
              class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all relative z-0"
              :class="currentCategory === cat.slug ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
          >
              {{ cat.name }}
          </Link>
          <!-- Spacer for scroll padding -->
          <div class="flex-shrink-0 w-8"></div>
        </div>
      </div>

      <!-- Trending Now Horizontal Scroll - Mobile Only -->
      <div v-if="$page.props.popular_posts && $page.props.popular_posts.length > 0" class="md:hidden space-y-3">
        <div class="flex items-center justify-between px-1">
          <span class="text-sm font-bold text-gray-900 flex items-center">
            <span class="text-red-500 mr-1.5 text-base">🔥</span> 실시간 인기
          </span>
          <Link :href="route('popular')" class="text-[11px] font-bold text-gray-400 hover:text-indigo-600 transition-colors">전체보기 &rarr;</Link>
        </div>
        <div class="flex overflow-x-auto gap-4 pb-4 -mx-4 px-4 scrollbar-hide no-scrollbar">
          <div 
            v-for="(post, index) in $page.props.popular_posts" 
            :key="'trending-' + post.id"
            class="flex-shrink-0 w-64 bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col justify-between"
          >
            <div class="flex items-start justify-between mb-2">
              <span class="text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-lg" :class="index === 0 ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-400'">
                {{ index + 1 }}
              </span>
              <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-full">{{ post.score }}점</span>
            </div>
            <Link :href="route('posts.show', post.id)" class="text-sm font-bold text-gray-800 line-clamp-2 hover:text-indigo-600 transition-colors leading-snug h-10 mb-2">
              {{ post.title }}
            </Link>
            <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-50">
              <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap overflow-hidden truncate max-w-[80px]">Hot Topics</span>
              <button class="text-indigo-600 text-[10px] font-bold">읽어보기 &rarr;</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Desktop Search (Visible on md+) -->
      <div class="hidden md:flex bg-white p-4 rounded-xl shadow-sm border border-gray-200 gap-3">
          <select 
             v-model="searchType" 
             class="border-gray-200 text-gray-700 font-medium rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-40 text-sm"
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

      <!-- Mobile Search / Filter Toggle & Section -->
      <div class="md:hidden space-y-3">
        <div class="flex items-center justify-between px-1">
          <button 
            @click="toggleSearch"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl border transition-all duration-200 shadow-sm"
            :class="isSearchVisible 
              ? 'bg-indigo-600 border-indigo-600 text-white shadow-indigo-100' 
              : 'bg-white border-gray-200 text-gray-700 hover:border-indigo-300 hover:bg-gray-50'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="text-xs font-black tracking-tight">{{ isSearchVisible ? '검색창 닫기' : '필터 및 검색하기' }}</span>
          </button>
          
          <div v-if="props.filters?.search_keyword && !isSearchVisible" class="flex flex-col items-end">
            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Active Search</span>
            <span class="text-[11px] bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg font-black border border-indigo-100">
              "{{ props.filters.search_keyword }}"
            </span>
          </div>
        </div>

        <div 
          v-show="isSearchVisible" 
          class="bg-white p-3 rounded-xl shadow-sm border border-indigo-100 flex flex-col gap-2 transition-all duration-300 overflow-hidden"
        >
          <div class="flex gap-2">
            <select 
               v-model="searchType" 
               class="border-gray-200 text-gray-700 font-medium rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 flex-1 text-sm py-2"
            >
                <option value="title">제목</option>
                <option value="tags">태그</option>
                <option value="author">작성자</option>
            </select>
          </div>
          <div class="relative">
              <input 
                 v-model="searchKeyword" 
                 @keydown.enter="executeSearch"
                 type="text" 
                 placeholder="검색어를 입력하세요..." 
                 class="w-full border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-12 text-sm py-2"
              />
              <button @click="executeSearch" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 bg-indigo-600 text-white rounded-md transition-colors shadow-sm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </button>
          </div>
        </div>
      </div>

      <!-- Pinned Notices (모든 카테고리에서 항상 표시) -->
      <div v-if="pinnedNotices && pinnedNotices.length > 0" class="space-y-4">
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
                :comments="notice.comments"
                :views="notice.views"
                :author-tier-name="notice.authorTierName"
                :author-tier-icon="notice.authorTierIcon"
                :type="'notice'"
                :is-pinned="true"
                class="border-l-4 border-l-red-500"
              />
          </div>
      </div>

      <div v-if="postList && postList.length > 0" :class="containerClass">
          <div v-if="currentCategory === 'all' && pinnedNotices && pinnedNotices.length > 0" class="flex items-center space-x-2 px-1 mt-4 w-full h-fit">
              <span class="text-sm font-bold text-gray-400">전체 피드</span>
              <div class="h-0.5 flex-1 bg-gray-50 rounded-full"></div>
          </div>
          
          <template v-for="(post, index) in postList" :key="post.id">
            <component 
              :is="getCardComponent(post)"
              :id="post.id"
              :author-name="post.authorName"
              :author-avatar="post.authorAvatar"
              :time-ago="post.timeAgo"
              :category="post.category"
              :category-slug="post.categorySlug"
              :tags="post.tags"
              :title="post.title"
              :summary="post.summary"
              :likes="post.likes"
              :comments="post.comments"
              :views="post.views"
              :type="post.type"
              :is-pinned="post.isPinned"
              :author-tier-name="post.authorTierName"
              :author-tier-icon="post.authorTierIcon"
              :extra_info="post.extra_info"
              :card_image_path="post.card_image_path"
              :card_image_url="post.card_image_url"
            />

            <!-- Mobile Injection: Widgets only for 'all' or specific indices -->
            <template v-if="currentCategory === 'all'">
                <!-- Mobile Injection: Hall of Fame (after index 4) -->
                <div v-if="index === 4" class="md:hidden py-2 col-span-full">
                  <div class="flex items-center space-x-2 px-1 mb-4">
                    <span class="text-xs font-bold text-gray-400">오늘의 명예의 전당</span>
                    <div class="h-px flex-1 bg-gray-100"></div>
                  </div>
                  <HallOfFameWidget :rankings="props.rankings || []" />
                </div>
    
                <!-- Mobile Injection: Partner Sites (after index 9) -->
                <div v-if="index === 9" class="md:hidden py-2 col-span-full">
                  <div class="flex items-center space-x-2 px-1 mb-4">
                    <span class="text-xs font-bold text-gray-400">추천 커뮤니티</span>
                    <div class="h-px flex-1 bg-gray-100"></div>
                  </div>
                  <PartnerSitesWidget />
                </div>
            </template>
          </template>
      </div>

      <!-- 무한 스크롤 옵저버 타겟 -->
      <div ref="observerTarget" class="h-10 w-full" v-show="nextPageUrl">
        <div v-if="isLoading" class="flex justify-center items-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>
      </div>

      <!-- Empty State -->
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
