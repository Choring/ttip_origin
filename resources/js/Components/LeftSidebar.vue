<script setup>
import { usePage, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useToast } from '@/Composables/useToast';

const page = usePage();
const { showToast } = useToast();

const categories = computed(() => (page.props.categories || []).filter(cat => cat.slug !== 'notice'));
const subscribedTags = computed(() => page.props.subscribed_tags || []);
const isLoggedIn = computed(() => !!page.props.auth?.user);

const currentCategory = computed(() => {
    if (route().current('posts.show')) {
        return page.props.post?.category?.slug || null;
    }
    if (route().current('home')) {
        return page.props.currentCategory || 'all';
    }
    return null;
});

// ── 태그 추가 ──────────────────────────────────────────────────────────────
const showTagInput  = ref(false);
const tagInput      = ref('');
const suggestions   = ref([]);
const showDropdown  = ref(false);
let debounceTimer   = null;

const openTagInput = () => {
    showTagInput.value = true;
    setTimeout(() => document.getElementById('sidebar-tag-input')?.focus(), 50);
};

const closeTagInput = () => {
    showTagInput.value = false;
    tagInput.value     = '';
    suggestions.value  = [];
    showDropdown.value = false;
};

const onTagInput = () => {
    clearTimeout(debounceTimer);
    const q = tagInput.value.trim();

    if (q.length < 1) {
        suggestions.value  = [];
        showDropdown.value = false;
        return;
    }

    debounceTimer = setTimeout(async () => {
        try {
            const res = await fetch(`/api/tags/suggest?q=${encodeURIComponent(q)}`);
            suggestions.value  = await res.json();
            showDropdown.value = suggestions.value.length > 0;
        } catch {
            suggestions.value  = [];
            showDropdown.value = false;
        }
    }, 250);
};

const subscribeTag = (tag) => {
    if (!tag.trim()) return;

    // 이미 구독 중인 태그
    if (subscribedTags.value.includes(tag)) {
        showToast(`#${tag} 는 이미 관심 태그에 있어요.`);
        closeTagInput();
        return;
    }

    router.post(route('tags.subscribe', tag), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showToast(`#${tag} 관심 태그에 추가했어요! 🔔`);
            closeTagInput();
        },
        onError: () => {
            showToast('구독에 실패했습니다. 다시 시도해 주세요.', 'error');
        },
    });
};

const selectSuggestion = (tag) => {
    tagInput.value     = tag;
    showDropdown.value = false;
    subscribeTag(tag);
};

const onTagKeydown = (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        subscribeTag(tagInput.value.trim());
    }
    if (e.key === 'Escape') {
        closeTagInput();
    }
};

const hideSuggestions = () => {
    setTimeout(() => { showDropdown.value = false; }, 150);
};

// 태그 구독 취소
const unsubscribeTag = (tag, e) => {
    e.preventDefault();
    e.stopPropagation();
    router.post(route('tags.subscribe', tag), {}, {
        preserveScroll: true,
        onSuccess: () => showToast(`#${tag} 관심 태그에서 제거했어요.`),
        onError: () => showToast('구독 취소에 실패했습니다.', 'error'),
    });
};
</script>

<template>
  <div class="flex flex-col gap-6">
    <!-- Categories -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
      <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">카테고리</h3>
      <nav class="space-y-1">
        <Link
          :href="route('home')"
          class="group flex items-center gap-2 px-3 py-2 text-sm rounded-r-xl rounded-l-sm transition-colors"
          :class="currentCategory === 'all' ? 'bg-gray-50 border-l-4 border-indigo-600 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-300 hover:text-gray-900 font-semibold'"
        >
          <span class="truncate">전체 피드</span>
        </Link>
        <Link
          v-for="cat in categories"
          :key="cat.id"
          :href="route('home', { category: cat.slug })"
          class="group flex items-center gap-2 px-3 py-2 text-sm rounded-r-xl rounded-l-sm transition-colors"
          :class="currentCategory === cat.slug ? 'bg-gray-50 border-l-4 border-indigo-600 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-300 hover:text-gray-900 font-semibold'"
        >
          <span class="truncate">{{ cat.name }}</span>
        </Link>
      </nav>
    </div>

    <!-- 관심 태그 (로그인 유저만) -->
    <div v-if="isLoggedIn" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
      <!-- 헤더 -->
      <div class="flex items-center justify-between mb-3 px-2">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">관심 태그</h3>
        <button
          v-if="!showTagInput"
          @click="openTagInput"
          class="w-5 h-5 flex items-center justify-center rounded-full bg-indigo-50 text-indigo-500 hover:bg-indigo-100 transition-colors"
          title="태그 추가"
        >
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
        </button>
        <button
          v-else
          @click="closeTagInput"
          class="w-5 h-5 flex items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 transition-colors"
          title="닫기"
        >
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>

      <!-- 태그 입력창 -->
      <div v-if="showTagInput" class="relative mb-2 px-1">
        <input
          id="sidebar-tag-input"
          v-model="tagInput"
          @input="onTagInput"
          @keydown="onTagKeydown"
          @blur="hideSuggestions"
          type="text"
          placeholder="태그 검색 후 Enter"
          class="w-full text-xs border border-indigo-200 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-400 bg-indigo-50 placeholder-gray-400"
        />
        <!-- 자동완성 드롭다운 -->
        <div
          v-if="showDropdown"
          class="absolute left-1 right-1 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-20 overflow-hidden"
        >
          <button
            v-for="s in suggestions"
            :key="s.tag"
            @mousedown.prevent="selectSuggestion(s.tag)"
            class="w-full text-left px-3 py-2 text-xs hover:bg-indigo-50 transition-colors flex items-center gap-2"
          >
            <span class="text-indigo-400 font-bold">#</span>
            <span class="text-gray-700 font-semibold">{{ s.tag }}</span>
          </button>
        </div>
      </div>

      <!-- 구독 태그 목록 -->
      <nav v-if="subscribedTags.length > 0" class="space-y-1">
        <div
          v-for="tag in subscribedTags"
          :key="tag"
          class="group flex items-center gap-2 px-3 py-2 text-sm rounded-xl transition-colors font-semibold"
          :class="route().current('tags.show') && $page.props.tag === tag
            ? 'bg-indigo-50 text-indigo-700'
            : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600'"
        >
          <Link :href="route('tags.show', tag)" class="flex items-center gap-1.5 flex-1 min-w-0">
            <span class="text-indigo-400 text-xs">#</span>
            <span class="truncate">{{ tag }}</span>
          </Link>
          <!-- 구독 취소 (X 버튼, hover 시 표시) -->
          <button
            @click="unsubscribeTag(tag, $event)"
            class="opacity-0 group-hover:opacity-100 text-gray-300 hover:text-red-400 transition-all flex-shrink-0"
            title="구독 취소"
          >
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
      </nav>

      <!-- 구독 태그 없을 때 -->
      <p v-else-if="!showTagInput" class="text-xs text-gray-400 px-2 py-1">
        + 버튼으로 관심 태그를 추가해보세요
      </p>
    </div>

    <!-- Advertisement (실제 광고 있을 때만 표시) -->
    <div v-if="$page.props.advertisement" class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 h-64 flex flex-col items-center justify-center text-gray-400">
      <svg class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
      </svg>
      <span class="text-xs font-bold">ADVERTISEMENT</span>
    </div>
  </div>
</template>
