<template>
  <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
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

        <!-- Right Sidebar (Discovery Widgets) -->
        <div class="hidden md:block md:col-span-3 lg:col-span-3">
          <RightSidebar />
        </div>
      </div>
    </div>

    <!-- Global Footer -->
    <AppFooter />

    <!-- Global Toast Notifications -->
    <ToastNotification />

    <!-- 티어 승급 축하 모달 -->
    <TierUpgradeModal
      v-if="tierUpgradeData"
      :tier="tierUpgradeData"
      @close="tierUpgradeData = null"
    />

  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';
import AppHeader from '@/Components/AppHeader.vue';
import LeftSidebar from '@/Components/LeftSidebar.vue';
import RightSidebar from '@/Components/RightSidebar.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import TierUpgradeModal from '@/Components/TierUpgradeModal.vue';
import AppFooter from '@/Components/AppFooter.vue';

const { showToast, showPointToast } = useToast();
const page = usePage();

// 포인트 적립 전용 황금 토스트
watch(() => page.props.flash?.point_gain, (gain) => {
    if (gain) showPointToast(gain);
}, { immediate: true });

// 일반 성공/에러 메시지도 토스트로 연동
watch(() => page.props.flash?.success, (msg) => {
    if (msg) showToast(msg, 'success');
});
watch(() => page.props.flash?.error, (msg) => {
    if (msg) showToast(msg, 'error');
});

// 티어 승급 모달 (Inertia 페이지 전환 시 flash로 전달)
const tierUpgradeData = ref(null);
watch(() => page.props.flash?.tier_upgrade, (data) => {
    if (data) tierUpgradeData.value = data;
}, { immediate: true });
</script>

