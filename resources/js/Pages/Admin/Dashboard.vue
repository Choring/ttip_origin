<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="관리자 대시보드" />

    <AdminLayout>
        <h1 class="text-2xl font-bold mb-6 text-gray-800">대시보드 요약</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
                <div class="text-gray-500 text-sm font-bold mb-3 uppercase tracking-wider">총 가입 회원 수</div>
                <div class="text-4xl font-extrabold text-gray-800">{{ stats.total_users }} <span class="text-base font-medium text-gray-400">명</span></div>
            </div>
            
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-md border border-indigo-200 p-6 flex flex-col justify-center text-white">
                <div class="text-indigo-100 text-sm font-bold mb-3 uppercase tracking-wider">오늘 신규 가입자</div>
                <div class="text-4xl font-extrabold">{{ stats.new_users_today }} <span class="text-base font-medium text-indigo-200">명</span></div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
                <div class="text-gray-500 text-sm font-bold mb-3 uppercase tracking-wider">누적 전체 게시글 수</div>
                <div class="text-4xl font-extrabold text-gray-800">{{ stats.total_posts }} <span class="text-base font-medium text-gray-400">개</span></div>
            </div>
        </div>
    </AdminLayout>
</template>
