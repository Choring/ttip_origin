<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const categories = computed(() => page.props.categories || []);

const currentCategory = computed(() => {
    return page.props.currentCategory || 'all';
});

const categoryIcons = {
    'restaurant':   '🍽️',
    'cafe':         '☕',
    'solo-dining':  '🥢',
    'gym':          '💪',
    'part-time':    '💼',
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
          <span class="text-base">📋</span>
          <span class="truncate">전체 피드</span>
        </Link>
        <Link
          v-for="cat in categories"
          :key="cat.id"
          :href="route('home', { category: cat.slug })"
          class="group flex items-center gap-2 px-3 py-2 text-sm rounded-r-xl rounded-l-sm transition-colors"
          :class="currentCategory === cat.slug ? 'bg-gray-50 border-l-4 border-indigo-600 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-300 hover:text-gray-900 font-semibold'"
        >
          <span class="text-base">{{ categoryIcons[cat.slug] || '📌' }}</span>
          <span class="truncate">{{ cat.name }}</span>
        </Link>
      </nav>
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
