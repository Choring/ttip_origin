<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import LoginModal from '@/Components/LoginModal.vue';

const navItems = [
  { name: '탐색', route: 'home' },
  { name: '공지사항', route: 'notices.index' },
  { name: '인기글', route: 'popular' },
  { name: '북마크', route: 'bookmarks' },
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
            <Link
              v-for="item in navItems"
              :key="item.route"
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
          </nav>
        </div>

        <!-- Spacer (데스크탑) -->
        <div class="hidden md:block flex-1"></div>

        <!-- ===== 우측 영역 ===== -->
        <div v-if="$page.props.auth.user" class="flex items-center space-x-3">
          <!-- 포인트 + 티어 뱃지 (데스크탑만) -->
          <Link :href="route('profile.edit')" class="hidden md:flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-full px-3 py-1.5 hover:bg-amber-100 transition-colors">
            <span class="text-base leading-none">{{ $page.props.auth.user.tier?.icon_url ?? '🌱' }}</span>
            <span class="text-xs font-black text-amber-700 tracking-tight">
              {{ ($page.props.auth.user.current_points ?? 0).toLocaleString() }}P
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
              {{ $page.props.auth.user.name.substring(0, 1) }}
            </div>
            <span class="text-sm font-bold text-gray-700">{{ $page.props.auth.user.name }}</span>
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
        <div v-if="$page.props.auth.user" class="px-5 py-5 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="bg-red-100 rounded-full h-11 w-11 flex items-center justify-center text-red-500 font-bold text-base flex-shrink-0">
              {{ $page.props.auth.user.name.substring(0, 1) }}
            </div>
            <div>
              <p class="font-bold text-gray-900 text-sm">{{ $page.props.auth.user.name }}</p>
              <p class="text-xs text-gray-400 mt-0.5">
                {{ $page.props.auth.user.tier?.icon_url ?? '🌱' }}
                {{ ($page.props.auth.user.current_points ?? 0).toLocaleString() }}P
              </p>
            </div>
          </div>
        </div>

        <!-- 네비게이션 -->
        <nav class="flex-1 px-3 py-4 space-y-1">
          <Link
            v-for="item in navItems"
            :key="item.route"
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
        </nav>

        <!-- 하단 로그아웃 -->
        <div class="px-5 py-5 border-t border-gray-100">
          <Link
            v-if="$page.props.auth.user"
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
