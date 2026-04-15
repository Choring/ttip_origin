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
</script>

<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo and Nav -->
        <div class="flex items-center space-x-8">
          <Link :href="tryCatchRoute('home')" class="flex-shrink-0 flex items-center pr-4">
            <span class="text-primary font-extrabold text-2xl tracking-tighter cursor-pointer">ttip</span>
          </Link>
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

        <!-- Spacer -->
        <div class="flex-1"></div>

        <!-- Right Side Nav -->
        <div v-if="$page.props.auth.user" class="flex items-center space-x-3">
          <!-- 포인트 + 티어 뱃지 -->
          <Link :href="route('profile.edit')" class="hidden sm:flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-full px-3 py-1.5 hover:bg-amber-100 transition-colors group">
            <span class="text-base leading-none" :title="$page.props.auth.user.tier?.name ?? '씨앗'">
              {{ $page.props.auth.user.tier?.icon_url ?? '🌱' }}
            </span>
            <span class="text-xs font-black text-amber-700 tracking-tight">
              {{ ($page.props.auth.user.current_points ?? 0).toLocaleString() }}P
            </span>
          </Link>

          <Link :href="route('posts.create')" class="bg-primary hover:bg-[#E65300] text-white px-4 py-2 rounded-full text-sm font-bold shadow-sm flex items-center transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            글쓰기
          </Link>

          <Link :href="route('profile.edit')" class="flex items-center bg-gray-50 border border-gray-100 rounded-full pr-3 pl-1 py-1 cursor-pointer hover:bg-gray-100 transition-colors">
            <div class="relative mr-2">
              <div class="bg-red-100 rounded-full h-7 w-7 flex items-center justify-center text-red-500 font-bold text-xs">
                {{ $page.props.auth.user.name.substring(0, 1) }}
              </div>
              <!-- 티어 이모지 오버레이 (모바일) -->
              <span class="absolute -bottom-1 -right-1 text-[10px] leading-none sm:hidden">
                {{ $page.props.auth.user.tier?.icon_url ?? '🌱' }}
              </span>
            </div>
            <span class="text-sm font-bold text-gray-700">{{ $page.props.auth.user.name }}</span>
          </Link>

          <Link :href="route('logout')" method="post" as="button" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">
            로그아웃
          </Link>
        </div>

        <div v-else class="flex items-center space-x-3">
          <button @click="showLoginModal = true" class="text-gray-600 hover:text-gray-900 font-semibold px-4 py-2 text-sm transition-colors">
            로그인
          </button>
          <Link :href="route('register')" class="bg-primary hover:bg-[#E65300] text-white px-5 py-2 rounded-full text-sm font-bold shadow-sm transition-colors">
            회원가입
          </Link>
        </div>
      </div>
    </div>
    
    <LoginModal :show="showLoginModal" @close="showLoginModal = false" />
  </header>
</template>
