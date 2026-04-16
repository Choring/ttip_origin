<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const showFab = ref(true);
let lastScrollY = 0;

const handleScroll = () => {
  const currentScrollY = window.scrollY;
  if (currentScrollY > lastScrollY && currentScrollY > 100) {
    showFab.value = false;
  } else {
    showFab.value = true;
  }
  lastScrollY = currentScrollY;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
  <div 
    class="md:hidden fixed bottom-6 right-6 z-40 transition-all duration-300 transform"
    :class="[
      showFab ? 'translate-y-0 opacity-100' : 'translate-y-20 opacity-0 pointer-events-none'
    ]"
  >
    <div class="relative group">
      <!-- Glow Effect -->
      <div class="absolute -inset-1 bg-gradient-to-r from-primary to-orange-400 rounded-full blur opacity-40 group-hover:opacity-60 transition duration-1000 group-hover:duration-200"></div>
      
      <Link 
        :href="route('posts.create')"
        class="relative flex items-center justify-center w-14 h-14 bg-primary text-white rounded-full shadow-2xl hover:bg-orange-600 transition-all active:scale-95"
      >
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
        </svg>
      </Link>
    </div>
  </div>
</template>
