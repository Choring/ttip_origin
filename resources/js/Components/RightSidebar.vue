<template>
  <div class="flex flex-col gap-6">
    <!-- Hall of Fame (명예의 전당) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-[15px] font-bold text-gray-900 flex items-center">
          <span class="text-yellow-500 mr-2 text-lg">🏆</span> 명예의 전당
        </h3>
        <span class="text-xs text-gray-400 font-medium">Top 3</span>
      </div>
      <ul class="space-y-4">
        <li class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
             <div class="relative">
               <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-50" src="https://ui-avatars.com/api/?name=User1&background=random" alt="">
               <span class="absolute -top-1 -left-1 bg-yellow-400 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white shadow-sm">1</span>
             </div>
             <div>
               <p class="text-sm font-bold text-gray-900">IT 위스퍼</p>
               <p class="text-xs font-semibold text-gray-500">12,400 P</p>
             </div>
          </div>
          <span class="text-yellow-500 text-xl" title="Crown">👑</span>
        </li>
        <li class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
             <div class="relative">
               <img class="h-10 w-10 rounded-full ring-2 ring-gray-50" src="https://ui-avatars.com/api/?name=User2&background=random" alt="">
               <span class="absolute -top-1 -left-1 bg-gray-400 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white shadow-sm">2</span>
             </div>
             <div>
               <p class="text-sm font-bold text-gray-900">김시니어</p>
               <p class="text-xs font-semibold text-gray-500">10,210 P</p>
             </div>
          </div>
        </li>
        <li class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
             <div class="relative">
               <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-50" src="https://ui-avatars.com/api/?name=User3&background=random" alt="">
               <span class="absolute -top-1 -left-1 bg-yellow-700 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white shadow-sm">3</span>
             </div>
             <div>
               <p class="text-sm font-bold text-gray-900">FIN테크</p>
               <p class="text-xs font-semibold text-gray-500">9,115 P</p>
             </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Trending Keywords -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <h3 class="text-[15px] font-bold text-gray-900 flex items-center mb-4">
        <span class="text-blue-500 mr-2 text-lg">📊</span> 실시간 인기 키워드
      </h3>
      <ul class="space-y-3">
        <li class="flex items-center justify-between group cursor-pointer hover:bg-gray-50 p-2 -mx-2 rounded-lg transition-colors">
          <div class="flex items-center space-x-3">
            <span class="text-primary text-xs font-bold bg-orange-100 w-5 h-5 flex items-center justify-center rounded-md">1</span>
            <span class="text-sm font-semibold text-gray-700 group-hover:text-primary transition-colors">ChatGPT 활용법</span>
          </div>
          <span class="text-xs font-medium text-gray-400">1.5k</span>
        </li>
        <li class="flex items-center justify-between group cursor-pointer hover:bg-gray-50 p-2 -mx-2 rounded-lg transition-colors">
          <div class="flex items-center space-x-3">
            <span class="text-secondary text-xs font-bold bg-green-100 w-5 h-5 flex items-center justify-center rounded-md">2</span>
            <span class="text-sm font-semibold text-gray-700 group-hover:text-secondary transition-colors">아이폰 설정 팁</span>
          </div>
          <span class="text-xs font-medium text-gray-400">1.1k</span>
        </li>
      </ul>
    </div>

    <!-- Advertisement Outline Right -->
    <div class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 h-80 flex flex-col items-center justify-center text-gray-400 relative overflow-hidden group">
      <svg class="h-8 w-8 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
      </svg>
      <span class="text-xs font-bold tracking-wider text-gray-400">ADVERTISEMENT</span>
      <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
        <button class="bg-gray-800/20 text-gray-600 rounded-full p-1.5 hover:bg-gray-800/40"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg></button>
      </div>
    </div>
  </div>
</template>
