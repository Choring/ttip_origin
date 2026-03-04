<script setup>
import { Link } from '@inertiajs/vue3';

const navItems = [
  { name: '탐색', route: 'home' },
  { name: '인기글', route: 'popular' },
  { name: '북마크', route: 'bookmarks' },
];

function isActive(routeName) {
  return route().current(routeName);
}
</script>

<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo and Nav -->
        <div class="flex items-center space-x-8">
          <Link :href="route('home')" class="flex-shrink-0 flex items-center pr-4">
            <span class="text-primary font-extrabold text-2xl tracking-tighter cursor-pointer">ttip</span>
          </Link>
          <nav class="hidden md:flex space-x-6 h-full">
            <Link
              v-for="item in navItems"
              :key="item.route"
              :href="route(item.route)"
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

        <!-- Search Bar -->
        <div class="flex-1 flex justify-center px-2 lg:ml-6 lg:justify-end">
          <div class="max-w-lg w-full lg:max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <!-- search icon -->
              <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                 <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
              </svg>
            </div>
            <input class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-full leading-5 bg-background text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary sm:text-sm transition duration-150 ease-in-out font-medium" placeholder="선택한 카테고리 내 검색 (Ctrl + K)" type="search">
          </div>
        </div>

        <!-- Right Side Nav -->
        <div class="flex items-center space-x-4">
          <button class="bg-primary hover:bg-[#E65300] text-white px-4 py-2 rounded-full text-sm font-bold shadow-sm flex items-center transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            글쓰기
          </button>
          
          <button class="text-gray-400 hover:text-gray-500 rounded-full p-2">
            <!-- bell icon -->
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </button>
          
          <div class="flex items-center bg-gray-50 border border-gray-100 rounded-full pr-3 pl-1 py-1 cursor-pointer hover:bg-gray-100 transition-colors">
            <div class="bg-red-100 rounded-full h-7 w-7 flex items-center justify-center text-red-500 font-bold mr-2 text-xs">
              닉
            </div>
            <span class="text-sm font-bold text-gray-700">닉네임</span>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
