<script setup>
import PopularPostsWidget from '@/Components/PopularPostsWidget.vue';
import HallOfFameWidget from '@/Components/HallOfFameWidget.vue';
import PartnerSitesWidget from '@/Components/PartnerSitesWidget.vue';
</script>

<template>
  <div class="flex flex-col gap-6">
    <!-- Hall of Fame Widget -->
    <HallOfFameWidget />

    <!-- Popular Posts Widget -->
    <PopularPostsWidget />

    <!-- Partner Sites Widget -->
    <PartnerSitesWidget />

    <!-- Advertisement Outline Right (Hidden on Mobile) -->
    <div class="hidden md:flex bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 h-80 flex-col items-center justify-center text-gray-400 relative overflow-hidden group">
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
