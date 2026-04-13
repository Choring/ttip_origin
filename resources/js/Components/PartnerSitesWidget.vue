<script setup>
const partnerSites = [
  {
    name: 'DopaStation',
    url: 'https://dopastation.com/',
    description: '도파민 가득한 커뮤니티 충전소',
    icon: '⚡'
  }
];
</script>

<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
    <h3 class="text-[15px] font-bold text-gray-900 flex items-center mb-4">
      <span class="text-indigo-500 mr-2 text-lg">🔗</span> 추천 사이트
    </h3>
    <div class="grid grid-cols-1 gap-3">
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
</template>
