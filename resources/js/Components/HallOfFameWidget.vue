<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-[15px] font-bold text-gray-900 flex items-center">
        <span class="text-yellow-500 mr-2 text-lg">🏆</span> 명예의 전당
      </h3>
      <span class="text-xs text-gray-400 font-medium">Top 3</span>
    </div>
    
    <ul class="space-y-4" v-if="page.props.hall_of_fame && page.props.hall_of_fame.length > 0">
      <li v-for="(user, index) in page.props.hall_of_fame" :key="user.id" class="flex items-center justify-between">
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
</template>
