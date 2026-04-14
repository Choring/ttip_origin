<script setup>
const props = defineProps({
  rankings: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-[15px] font-bold text-gray-900 flex items-center">
        <span class="text-yellow-500 mr-2 text-lg">🏆</span> 명예의 전당
      </h3>
      <span class="text-xs text-gray-400 font-medium font-black">Top 5</span>
    </div>
    
    <ul class="space-y-4" v-if="rankings && rankings.length > 0">
      <li v-for="(user, index) in rankings" :key="user.name" class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
           <div class="relative">
             <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-50" :src="user.avatar" :alt="user.name">
             <span 
               class="absolute -top-1 -left-1 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white shadow-sm"
               :class="{
                 'bg-yellow-400': index === 0,
                 'bg-gray-400': index === 1,
                 'bg-yellow-700': index === 2,
                 'bg-indigo-400': index > 2
               }"
             >
               {{ index + 1 }}
             </span>
           </div>
           <div>
             <div class="flex items-center space-x-1">
               <span v-if="user.tierIcon" :title="user.tierName" class="text-xs">{{ user.tierIcon }}</span>
               <p class="text-sm font-bold text-gray-900 leading-none">{{ user.name }}</p>
             </div>
             <p class="text-xs font-semibold text-gray-400 mt-1">{{ user.points.toLocaleString() }} P</p>
           </div>
        </div>
        <span v-if="index === 0" class="text-yellow-500 text-xl" title="Crown">👑</span>
      </li>
    </ul>
    <div v-else class="text-xs text-gray-400 text-center py-4 font-medium">명예의 전당 데이터가 없습니다.</div>
  </div>
</template>

