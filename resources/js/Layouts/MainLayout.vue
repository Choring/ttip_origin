<template>
  <div class="min-h-screen bg-background font-sans text-gray-900">
    <AppHeader />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Left Sidebar -->
        <div class="hidden md:block md:col-span-3 lg:col-span-2">
          <LeftSidebar />
        </div>

        <!-- Main Content (Slot) -->
        <main class="md:col-span-6 lg:col-span-7">
          <slot />
        </main>

        <!-- Right Sidebar -->
        <div class="hidden md:block md:col-span-3 lg:col-span-3">
          <RightSidebar />
        </div>
      </div>
    </div>

    <!-- Global Footer -->
    <AppFooter />

    <!-- Global Toast Notifications -->
    <ToastNotification />
  </div>
</template>

<script setup>
import AppHeader from '@/Components/AppHeader.vue';
import LeftSidebar from '@/Components/LeftSidebar.vue';
import RightSidebar from '@/Components/RightSidebar.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>
