<template>
  <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200"
    style="padding-bottom: env(safe-area-inset-bottom);"
  >
    <div class="flex items-center justify-around h-16 px-2">

      <!-- 홈 -->
      <Link :href="route('home')" class="flex flex-col items-center gap-0.5 flex-1 py-2"
        :class="isActive('home') ? 'text-orange-500' : 'text-gray-400'"
      >
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        <span class="text-[10px] font-bold">홈</span>
      </Link>

      <!-- 행사 -->
      <Link :href="route('events.index')" class="flex flex-col items-center gap-0.5 flex-1 py-2"
        :class="isActive('events.index') ? 'text-orange-500' : 'text-gray-400'"
      >
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span class="text-[10px] font-bold">행사</span>
      </Link>

      <!-- 글쓰기 (중앙 강조 버튼) -->
      <div class="flex-1 flex justify-center">
        <button
          @click="onWrite"
          class="w-14 h-14 -mt-5 bg-orange-500 text-white rounded-full shadow-lg flex items-center justify-center active:scale-95 transition-transform"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
          </svg>
        </button>
      </div>

      <!-- 커뮤니티 -->
      <Link :href="route('community')" class="flex flex-col items-center gap-0.5 flex-1 py-2"
        :class="isActive('community') ? 'text-orange-500' : 'text-gray-400'"
      >
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span class="text-[10px] font-bold">커뮤니티</span>
      </Link>

      <!-- 프로필 -->
      <button v-if="!user" @click="showLoginModal = true"
        class="flex flex-col items-center gap-0.5 flex-1 py-2 text-gray-400"
      >
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <span class="text-[10px] font-bold">프로필</span>
      </button>
      <Link v-else :href="route('profile.edit')"
        class="flex flex-col items-center gap-0.5 flex-1 py-2"
        :class="isActive('profile.edit') ? 'text-orange-500' : 'text-gray-400'"
      >
        <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-black text-xs">
          {{ user.name?.charAt(0) }}
        </div>
        <span class="text-[10px] font-bold">프로필</span>
      </Link>

    </div>
  </nav>

  <!-- 로그인 모달 -->
  <LoginModal :show="showLoginModal" @close="showLoginModal = false" />
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import LoginModal from '@/Components/LoginModal.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const showLoginModal = ref(false);

const isActive = (routeName) => {
  try { return route().current(routeName); } catch { return false; }
};

const onWrite = () => {
  if (!user.value) {
    showLoginModal.value = true;
    return;
  }
  router.visit(route('posts.create'));
};
</script>
