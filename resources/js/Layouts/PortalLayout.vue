<template>
  <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
    <AppHeader />

    <!-- 풀 와이드 히어로 슬롯 -->
    <slot name="hero" />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>

    <AppFooter />
    <ToastNotification />

    <TierUpgradeModal
      v-if="tierUpgradeData"
      :tier="tierUpgradeData"
      @close="tierUpgradeData = null"
    />

    <PwaInstallPrompt />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import TierUpgradeModal from '@/Components/TierUpgradeModal.vue';
import PwaInstallPrompt from '@/Components/PwaInstallPrompt.vue';

const { showToast, showPointToast } = useToast();
const page = usePage();

watch(() => page.props.flash?.point_gain, (gain) => {
    if (gain) showPointToast(gain);
}, { immediate: true });

watch(() => page.props.flash?.success, (msg) => {
    if (msg) showToast(msg, 'success');
});
watch(() => page.props.flash?.error, (msg) => {
    if (msg) showToast(msg, 'error');
});

const tierUpgradeData = ref(null);
watch(() => page.props.flash?.tier_upgrade, (data) => {
    if (data) tierUpgradeData.value = data;
}, { immediate: true });
</script>
