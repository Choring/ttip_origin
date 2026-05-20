<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import LoginModal from '@/Components/LoginModal.vue';
import axios from 'axios';

const page = usePage();
const user = computed(() => page.props.auth.user);

const navItems = [
  { name: '탐색', route: 'home', requiresAuth: false },
  { name: '공지사항', route: 'notices.index', requiresAuth: false },
  { name: '인기글', route: 'popular', requiresAuth: false },
  { name: '북마크', route: 'bookmarks', requiresAuth: true },
  { name: '문화행사', route: 'events.index', requiresAuth: false },
  { name: '맛집', route: 'restaurants.index', requiresAuth: false },
  { name: '관광지', route: 'tour.index', requiresAuth: false }
];

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
        // 알림 목록에서 제거하거나 상태 변경
        notifications.value = notifications.value.filter(n => n.id !== notification.id);
        // 헤더 뱃지 갱신을 위해 Inertia 리로드 (서버 사이드에서 unread_notifications_count를 같이 내려주기 때문)
        router.reload({ only: ['auth'] });
        
        // 해당 URL로 이동
        if (notification.data.url) {
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
    // URL에서 쿼리 파라미터 제거
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

        <!-- Spacer (데스크탑) -->
        <div class="hidden md:block flex-1"></div>

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
                    class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 divide-y divide-gray-50 overflow-hidden z-[60]"
                >
                    <div class="px-4 py-3 flex items-center justify-between bg-gray-50/50">
                        <span class="text-sm font-bold text-gray-900">알림</span>
                        <button 
                            v-if="notifications.length > 0"
                            @click="markAllAsRead" 
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-bold"
                        >
                            모두 읽음
                        </button>
                    </div>

                    <div class="max-h-[400px] overflow-y-auto">
                        <div v-if="loadingNotifications" class="p-8 text-center">
                            <div class="inline-block animate-spin rounded-full h-5 w-5 border-2 border-indigo-500 border-t-transparent"></div>
                        </div>
                        <template v-else-if="notifications.length > 0">
                            <div 
                                v-for="notification in notifications" 
                                :key="notification.id"
                                @click="markAsRead(notification)"
                                class="px-4 py-4 hover:bg-indigo-50/30 cursor-pointer transition-colors flex gap-3 border-b border-gray-50 last:border-0"
                            >
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-xs">
                                    {{ notification.data.user_name?.substring(0, 1) || 'T' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate mb-0.5">{{ notification.data.title }}</p>
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ notification.data.message }}</p>
                                    <span class="text-[10px] text-gray-300 mt-1 block">방금 전</span>
                                </div>
                            </div>
                        </template>
                        <div v-else class="p-10 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <p class="text-sm font-medium">새로운 알림이 없습니다.</p>
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

          <!-- 글쓰기 버튼 -->
          <Link :href="route('posts.create')" class="bg-primary hover:bg-[#E65300] text-white px-3 md:px-4 py-2 rounded-full text-sm font-bold shadow-sm flex items-center transition-colors">
            <svg class="w-4 h-4 md:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            <span class="hidden md:inline">글쓰기</span>
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
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: transform 0.25s ease; }
.slide-enter-from, .slide-leave-to { transform: translateX(-100%); }
</style>
