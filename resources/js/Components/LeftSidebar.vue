<template>
  <div class="flex flex-col gap-6">
    <!-- Categories -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
      <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-2">카테고리</h3>
      <nav class="space-y-1">
        <a href="#" class="bg-gray-50 border-l-4 border-primary text-gray-900 group flex items-center px-3 py-2 text-sm font-bold rounded-r-xl rounded-l-sm">
          <span class="truncate">💻 IT/개발</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-300 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-semibold rounded-r-xl rounded-l-sm transition-colors">
          <span class="truncate">💡 생활/꿀팁</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-300 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-semibold rounded-r-xl rounded-l-sm transition-colors">
          <span class="truncate">🎨 디자인/기획</span>
        </a>
        <a href="#" class="text-gray-600 hover:bg-gray-50 border-l-4 border-transparent hover:border-gray-300 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-semibold rounded-r-xl rounded-l-sm transition-colors">
          <span class="truncate">🎬 취미/문화</span>
        </a>
      </nav>
    </div>

    <!-- Advertisement Outline -->
    <div class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 h-64 flex flex-col items-center justify-center text-gray-400">
      <svg class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
      </svg>
      <span class="text-xs font-bold">ADVERTISEMENT</span>
    </div>
  </div>
</template>
