<script setup>
import { Link } from '@inertiajs/vue3';

const partnerSites = [
  {
    name: 'DopaStation',
    url: 'https://dopastation.com/',
    description: '도파민 가득한 커뮤니티 정거장',
    icon: '⚡',
    color: 'text-yellow-500'
  },
  // 추가 지인 사이트가 있다면 여기에 추가 가능합니다.
];
</script>

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
      <ul class="space-y-4" v-if="$page.props.hall_of_fame && $page.props.hall_of_fame.length > 0">
        <li v-for="(user, index) in $page.props.hall_of_fame" :key="user.id" class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
             <div class="relative">
               <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-50" :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`" :alt="user.name">
               <span 
                 class="absolute -top-1 -left-1 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white shadow-sm"
                 :class="{
                   'bg-yellow-400': index === 0,
                   'bg-gray-400': index === 1,
                   'bg-yellow-700': index === 2
                 }"
               >
                 {{ index + 1 }}
               </span>
             </div>
             <div>
               <p class="text-sm font-bold text-gray-900">{{ user.name }}</p>
               <p class="text-xs font-semibold text-gray-500">{{ user.current_points.toLocaleString() }} P</p>
             </div>
          </div>
          <span v-if="index === 0" class="text-yellow-500 text-xl" title="Crown">👑</span>
        </li>
      </ul>
      <div v-else class="text-xs text-gray-400 text-center py-4 font-medium">명예의 전당 데이터가 없습니다.</div>
    </div>

    <!-- Trending Posts -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <h3 class="text-[15px] font-bold text-gray-900 flex items-center mb-4">
        <span class="text-red-500 mr-2 text-lg">🔥</span> 실시간 인기 게시글
      </h3>
      <ul class="space-y-3">
        <li v-for="(post, index) in $page.props.popular_posts" :key="post.id" class="flex items-center justify-between group p-2 -mx-2 rounded-lg transition-colors hover:bg-gray-50">
          <div class="flex items-center space-x-3 overflow-hidden flex-1 pr-2">
            <span class="flex-shrink-0 text-xs font-bold w-5 h-5 flex items-center justify-center rounded-md" :class="index === 0 ? 'bg-orange-100 text-orange-600' : (index === 1 ? 'bg-gray-200 text-gray-700' : 'bg-indigo-50 text-indigo-500')">{{ index + 1 }}</span>
            <Link :href="route('posts.show', post.id)" class="text-sm font-semibold text-gray-700 group-hover:text-primary transition-colors truncate block w-full">
                {{ post.title }}
            </Link>
          </div>
          <span class="flex-shrink-0 text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-100">점수 {{ post.score }}</span>
        </li>
      </ul>
      <div v-if="!$page.props.popular_posts || $page.props.popular_posts.length === 0" class="text-xs text-gray-400 text-center py-4 font-medium">등록된 인기 게시글이 없습니다.</div>
    </div>

    <!-- Partner Sites (추천 사이트) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <h3 class="text-[15px] font-bold text-gray-900 flex items-center mb-4">
        <span class="text-indigo-500 mr-2 text-lg">🔗</span> 추천 사이트
      </h3>
      <div class="space-y-3">
        <a 
          v-for="site in partnerSites" 
          :key="site.url" 
          :href="site.url" 
          target="_blank" 
          class="flex items-center p-3 rounded-xl border border-gray-50 bg-gray-50/50 hover:bg-indigo-50 hover:border-indigo-100 transition-all group"
        >
          <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center text-xl mr-3 group-hover:scale-110 transition-transform">
            {{ site.icon }}
          </div>
          <div class="flex-1 overflow-hidden">
            <div class="flex items-center justify-between">
              <span class="text-sm font-bold text-gray-800">{{ site.name }}</span>
              <svg class="w-3 h-3 text-gray-300 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
              </svg>
            </div>
            <p class="text-[11px] font-medium text-gray-500 truncate mt-0.5">{{ site.description }}</p>
          </div>
        </a>
      </div>
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
