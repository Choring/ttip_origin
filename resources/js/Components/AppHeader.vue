<script setup>
import { ref, onMounted, computed } from 'vue';

const fabOpen = ref(false);
const toggleFab = () => { fabOpen.value = !fabOpen.value; };
import { Link, usePage, router } from '@inertiajs/vue3';
import LoginModal from '@/Components/LoginModal.vue';
import axios from 'axios';

const page = usePage();
const user = computed(() => page.props.auth.user);

const navItems = [
  { name: '탐색',     route: 'home',              requiresAuth: false },
  { name: '공연·행사', route: 'events.index',       requiresAuth: false },
  { name: '맛집',     route: 'restaurants.index',  requiresAuth: false },
  { name: '관광지',   route: 'tour.index',          requiresAuth: false },
  { name: '커뮤니티', route: 'community',           requiresAuth: false },
];

// 헤더 검색
const searchQuery = ref('');
const submitSearch = () => {
  const q = searchQuery.value.trim();
  if (!q) return;
  router.get(route('home'), { search_keyword: q, search_type: 'title' });
  searchQuery.value = '';
};

function isActive(routeName) {
  try {
    return route().current(routeName);
  } catch (e) {
    return false;
  }
}

function tryCatchRoute(name) {
  try {
    return route(name);
  } catch (e) {
    return '#';
  }
}

const showLoginModal = ref(false);
const showMobileMenu = ref(false);
const showNotifications = ref(false);
const notifications = ref([]);
const loadingNotifications = ref(false);

// 알림 타입별 아이콘
const notificationIcon = (type) => {
    if (type?.includes('LikedNotification')) return '👍';
    if (type?.includes('PostCommented'))     return '💬';
    if (type?.includes('CommentReplied'))    return '↩️';
    return '🔔';
};

// 상대 시간 표시
const timeAgo = (dateStr) => {
    if (!dateStr) return '';
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60)     return '방금 전';
    if (diff < 3600)   return `${Math.floor(diff / 60)}분 전`;
    if (diff < 86400)  return `${Math.floor(diff / 3600)}시간 전`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}일 전`;
    return new Date(dateStr).toLocaleDateString('ko-KR', { month: 'short', day: 'numeric' });
};

const fetchNotifications = async () => {
    if (!user.value) return;
    loadingNotifications.value = true;
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data;
    } catch (e) {
        console.error('Failed to fetch notifications', e);
    } finally {
        loadingNotifications.value = false;
    }
};

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        fetchNotifications();
    }
};

const markAsRead = async (notification) => {
    try {
        await axios.post(route('notifications.read', notification.id));
        notifications.value = notifications.value.filter(n => n.id !== notification.id);
        router.reload({ only: ['auth'] });
        if (notification.data.url) {
            showNotifications.value = false;
            router.visit(notification.data.url);
        }
    } catch (e) {
        console.error('Failed to mark notification as read', e);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(route('notifications.readAll'));
        notifications.value = [];
        router.reload({ only: ['auth'] });
    } catch (e) {
        console.error('Failed to mark all as read', e);
    }
};

// 북마크 접근 시 리다이렉트된 경우 모달 자동 오픈
onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  if (params.get('showLogin') === '1') {
    showLoginModal.value = true;
    window.history.replaceState({}, '', window.location.pathname);
  }

  // 외부 클릭 시 알림 드롭다운 닫기
  window.addEventListener('click', (e) => {
      if (showNotifications.value && !e.target.closest('.notification-container')) {
          showNotifications.value = false;
      }
  });

});
</script>

<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">

        <!-- ===== 모바일 레이아웃 ===== -->
        <!-- 햄버거 (좌측) -->
        <button
          @click="showMobileMenu = true"
          class="md:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>

        <!-- 로고 (모바일 중앙 / 데스크탑 좌측) -->
        <div class="flex items-center md:space-x-8">
          <Link :href="tryCatchRoute('home')" class="flex-shrink-0 flex items-center md:pr-4 absolute left-1/2 -translate-x-1/2 md:static md:translate-x-0">
            <span class="text-primary font-extrabold text-2xl tracking-tighter cursor-pointer">ttip</span>
          </Link>

          <!-- 데스크탑 네비게이션 -->
          <nav class="hidden md:flex space-x-6 h-full">
            <template v-for="item in navItems" :key="item.route">
              <!-- 인증 필요 + 비로그인 → 버튼으로 렌더링 -->
              <button
                v-if="item.requiresAuth && !user"
                @click="showLoginModal = true"
                :class="[
                  'inline-flex items-center px-1 pt-1 text-sm h-16 border-b-2 transition-colors',
                  'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold'
                ]"
              >
                {{ item.name }}
              </button>
              <!-- 일반 링크 -->
              <Link
                v-else
                :href="tryCatchRoute(item.route)"
                :class="[
                  'inline-flex items-center px-1 pt-1 text-sm h-16 border-b-2 transition-colors',
                  isActive(item.route)
                    ? 'border-primary text-gray-900 font-bold'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold'
                ]"
              >
                {{ item.name }}
              </Link>
            </template>
          </nav>
        </div>

        <!-- 검색창 (데스크탑 중앙) - TODO: 통합 검색 페이지 준비 후 활성화 -->
        <div class="hidden flex-1 max-w-sm mx-6">
          <form @submit.prevent="submitSearch" class="w-full relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="대구 검색..."
              class="w-full pl-4 pr-10 py-2 text-sm bg-gray-100 rounded-full border border-transparent focus:outline-none focus:border-orange-300 focus:bg-white transition-all"
            />
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </button>
          </form>
        </div>

        <!-- ===== 우측 영역 ===== -->
        <div v-if="user" class="flex items-center space-x-3">
          <!-- 알림 아이콘 -->
          <div class="relative notification-container">
            <button 
                @click="toggleNotifications"
                class="p-2 transition-all rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 relative"
                title="알림"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <!-- 읽지 않은 알림 뱃지 -->
                <span 
                    v-if="user.unread_notifications_count > 0" 
                    class="absolute top-1.5 right-1.5 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-black rounded-full border-2 border-white px-0.5"
                >
                    {{ user.unread_notifications_count > 99 ? '99+' : user.unread_notifications_count }}
                </span>
            </button>

            <!-- 알림 드롭다운 -->
            <Transition name="fade">
                <div
                    v-if="showNotifications"
                    class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-[60]"
                >
                    <!-- 헤더 -->
                    <div class="px-4 py-3 flex items-center justify-between bg-gray-50 border-b border-gray-100">
                        <span class="text-sm font-black text-gray-900">알림</span>
                        <button
                            v-if="notifications.length > 0"
                            @click="markAllAsRead"
                            class="text-xs text-indigo-500 hover:text-indigo-700 font-bold transition-colors"
                        >
                            모두 읽음 처리
                        </button>
                    </div>

                    <!-- 목록 -->
                    <div class="max-h-[420px] overflow-y-auto divide-y divide-gray-50">
                        <!-- 로딩 -->
                        <div v-if="loadingNotifications" class="p-8 flex justify-center">
                            <div class="animate-spin rounded-full h-5 w-5 border-2 border-indigo-400 border-t-transparent"></div>
                        </div>

                        <!-- 알림 아이템 -->
                        <template v-else-if="notifications.length > 0">
                            <div
                                v-for="notification in notifications"
                                :key="notification.id"
                                @click="markAsRead(notification)"
                                class="px-4 py-3.5 hover:bg-indigo-50/40 cursor-pointer transition-colors flex gap-3 items-start"
                            >
                                <!-- 타입 아이콘 -->
                                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-base leading-none">
                                    {{ notificationIcon(notification.type) }}
                                </div>
                                <!-- 내용 -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 mb-0.5">{{ notification.data.title }}</p>
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ notification.data.message }}</p>
                                    <span class="text-[10px] text-gray-300 mt-1.5 block">
                                        {{ notification.data.user_name }} · {{ timeAgo(notification.created_at) }}
                                    </span>
                                </div>
                                <!-- 읽지 않음 점 -->
                                <div class="flex-shrink-0 w-2 h-2 rounded-full bg-indigo-400 mt-1.5"></div>
                            </div>
                        </template>

                        <!-- 빈 상태 -->
                        <div v-else class="py-12 text-center text-gray-400">
                            <div class="text-4xl mb-3">🔔</div>
                            <p class="text-sm font-medium text-gray-400">새로운 알림이 없습니다.</p>
                        </div>
                    </div>
                </div>
            </Transition>
          </div>

          <!-- 포인트 + 티어 뱃지 (데스크탑만) -->
          <Link :href="route('profile.edit')" class="hidden md:flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-full px-3 py-1.5 hover:bg-amber-100 transition-colors">
            <span class="text-base leading-none">{{ user.tier?.icon_url ?? '🌱' }}</span>
            <span class="text-xs font-black text-amber-700 tracking-tight">
              {{ (user.current_points ?? 0).toLocaleString() }}P
            </span>
          </Link>

          <!-- 글쓰기 버튼 (데스크탑만) -->
          <Link :href="route('posts.create')" class="hidden md:flex bg-primary hover:bg-[#E65300] text-white px-4 py-2 rounded-full text-sm font-bold shadow-sm items-center transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            글쓰기
          </Link>

          <!-- 유저 정보 + 로그아웃 (데스크탑만) -->
          <Link :href="route('profile.edit')" class="hidden md:flex items-center bg-gray-50 border border-gray-100 rounded-full pr-3 pl-1 py-1 hover:bg-gray-100 transition-colors">
            <div class="bg-red-100 rounded-full h-7 w-7 flex items-center justify-center text-red-500 font-bold text-xs mr-2">
              {{ user.name.substring(0, 1) }}
            </div>
            <span class="text-sm font-bold text-gray-700">{{ user.name }}</span>
          </Link>
          <Link :href="route('logout')" method="post" as="button" class="hidden md:block text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">
            로그아웃
          </Link>
        </div>

        <div v-else class="flex items-center">
          <button @click="showLoginModal = true" class="bg-primary hover:bg-[#E65300] text-white px-4 py-2 rounded-full text-sm font-bold shadow-sm transition-colors">
            로그인
          </button>
        </div>
      </div>
    </div>

    <!-- ===== 모바일 햄버거 드로어 ===== -->
    <!-- 오버레이 -->
    <Transition name="fade">
      <div
        v-if="showMobileMenu"
        @click="showMobileMenu = false"
        class="fixed inset-0 bg-black/40 z-50 md:hidden"
      />
    </Transition>

    <!-- 드로어 -->
    <Transition name="slide">
      <div
        v-if="showMobileMenu"
        class="fixed top-0 left-0 h-full w-72 bg-white z-50 shadow-2xl flex flex-col md:hidden"
      >
        <!-- 드로어 헤더 -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <span class="text-primary font-extrabold text-xl tracking-tighter">ttip</span>
          <button @click="showMobileMenu = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- 유저 정보 -->
        <div v-if="user" class="px-5 py-5 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="bg-red-100 rounded-full h-11 w-11 flex items-center justify-center text-red-500 font-bold text-base flex-shrink-0">
              {{ user.name.substring(0, 1) }}
            </div>
            <div>
              <p class="font-bold text-gray-900 text-sm">{{ user.name }}</p>
              <p class="text-xs text-gray-400 mt-0.5">
                {{ user.tier?.icon_url ?? '🌱' }}
                {{ (user.current_points ?? 0).toLocaleString() }}P
              </p>
            </div>
          </div>
        </div>

        <!-- 네비게이션 -->
        <nav class="flex-1 px-3 py-4 space-y-1">
          <template v-for="item in navItems" :key="item.route">
            <!-- 인증 필요 + 비로그인 → 버튼 -->
            <button
              v-if="item.requiresAuth && !user"
              @click="showMobileMenu = false; showLoginModal = true"
              class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-colors text-gray-600 hover:bg-gray-50 w-full text-left"
            >
              {{ item.name }}
            </button>
            <!-- 일반 링크 -->
            <Link
              v-else
              :href="tryCatchRoute(item.route)"
              @click="showMobileMenu = false"
              :class="[
                'flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-colors',
                isActive(item.route)
                  ? 'bg-primary/10 text-primary font-bold'
                  : 'text-gray-600 hover:bg-gray-50'
              ]"
            >
              {{ item.name }}
            </Link>
          </template>
        </nav>

        <!-- 모바일 검색창 - TODO: 통합 검색 페이지 준비 후 활성화 -->
        <div class="hidden px-4 py-3 border-b border-gray-100">
          <form @submit.prevent="submitSearch; showMobileMenu = false" class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="대구 검색..."
              class="w-full pl-4 pr-10 py-2.5 text-sm bg-gray-100 rounded-full border border-transparent focus:outline-none focus:border-orange-300 focus:bg-white transition-all"
            />
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </button>
          </form>
        </div>

        <!-- 문의하기 -->
        <div class="px-5 pb-2">
            <Link :href="tryCatchRoute('inquiry.create')" @click="showMobileMenu = false"
                class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-1-4z"/>
                </svg>
                문의하기
            </Link>
        </div>

        <!-- 하단 로그아웃 -->
        <div class="px-5 py-5 border-t border-gray-100">
          <Link
            v-if="user"
            :href="route('logout')"
            method="post"
            as="button"
            class="w-full flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            로그아웃
          </Link>
          <div v-else class="space-y-2">
            <button @click="showLoginModal = true; showMobileMenu = false" class="w-full px-4 py-3 rounded-xl text-sm font-bold text-gray-700 border border-gray-200 hover:bg-gray-50 transition-colors">
              로그인
            </button>
            <Link :href="route('register')" class="w-full flex justify-center px-4 py-3 rounded-xl text-sm font-bold bg-primary text-white hover:bg-[#E65300] transition-colors">
              회원가입
            </Link>
          </div>
        </div>
      </div>
    </Transition>

    <LoginModal :show="showLoginModal" @close="showLoginModal = false" />
  </header>

  <!-- ===== 데스크탑 플로팅 문의 버튼 (md 이상) ===== -->
  <Link :href="tryCatchRoute('inquiry.create')"
      class="hidden md:flex fixed bottom-8 right-8 z-40 items-center gap-2 px-4 py-3 bg-white border border-gray-200 text-gray-700 rounded-full shadow-lg hover:shadow-xl hover:border-primary hover:text-primary transition-all duration-300 text-sm font-bold"
  >
      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-1-4z"/>
      </svg>
      문의하기
  </Link>

  <!-- ===== 모바일 스피드 다이얼 FAB (md 미만) ===== -->
  <div class="md:hidden fixed bottom-6 right-6 z-40 flex flex-col items-end gap-3">

    <!-- 서브 버튼들 -->
    <div
      class="flex flex-col items-end gap-2 transition-all duration-200 origin-bottom-right"
      :class="fabOpen ? 'opacity-100 scale-100 pointer-events-auto' : 'opacity-0 scale-95 pointer-events-none'"
    >

      <!-- 글쓰기 -->
      <button
          @click="fabOpen = false; user ? router.visit(route('posts.create')) : (showLoginModal = true)"
          class="flex items-center gap-2.5 pl-4 pr-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-full shadow-md hover:shadow-lg hover:border-primary hover:text-primary transition-all duration-200 text-sm font-bold whitespace-nowrap">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
          </svg>
          글쓰기
      </button>

      <!-- 문의하기 -->
      <Link :href="tryCatchRoute('inquiry.create')" @click="fabOpen = false"
          class="flex items-center gap-2.5 pl-4 pr-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-full shadow-md hover:shadow-lg hover:border-primary hover:text-primary transition-all duration-200 text-sm font-bold whitespace-nowrap">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4-1-4z"/>
          </svg>
          문의하기
      </Link>

    </div>

    <!-- 메인 FAB 버튼 (글로우 효과 포함) -->
    <div class="relative group">
      <div class="absolute -inset-1 bg-gradient-to-r from-primary to-orange-400 rounded-full blur opacity-40 group-hover:opacity-60 transition duration-300"></div>
      <button @click="toggleFab"
          class="relative w-14 h-14 rounded-full bg-primary text-white shadow-2xl hover:bg-orange-600 transition-all duration-300 flex items-center justify-center active:scale-95"
          :class="fabOpen ? 'rotate-45' : ''"
      >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
      </button>
    </div>
  </div>

  <!-- FAB 열렸을 때 배경 딤 (모바일 전용) -->
  <Transition name="fade">
    <div v-if="fabOpen" @click="fabOpen = false"
        class="md:hidden fixed inset-0 z-30 bg-black/10 backdrop-blur-[1px]">
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: transform 0.25s ease; }
.slide-enter-from, .slide-leave-to { transform: translateX(-100%); }

</style>
