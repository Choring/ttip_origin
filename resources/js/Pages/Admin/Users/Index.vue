<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    users: Object,
});

const toggleBan = (user) => {
    if (confirm(`정말 이 회원을 ${user.is_banned ? '정지 해제' : '정지'} 처리하시겠습니까?`)) {
        router.patch(route('admin.users.toggleBan', user.id), {}, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="관리자 - 회원 관리" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">회원 관리</h1>
            <Link :href="route('admin.users.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold shadow transition-colors">
                + 신규 회원 추가
            </Link>
        </div>

        <!-- Success or Error Messages -->
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl font-medium shadow-sm border border-green-100">
            ✅ {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl font-medium shadow-sm border border-red-100">
            ⚠️ {{ $page.props.flash.error }}
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-sm border-b uppercase tracking-wider">
                        <th class="p-4 font-semibold w-16 text-center">ID</th>
                        <th class="p-4 font-semibold">이름(역할)</th>
                        <th class="p-4 font-semibold">이메일</th>
                        <th class="p-4 font-semibold">현재 티어</th>
                        <th class="p-4 font-semibold">활성 포인트</th>
                        <th class="p-4 font-semibold text-center">상태</th>
                        <th class="p-4 font-semibold text-center">관리 액션</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 text-sm transition-colors">
                        <td class="p-4 text-gray-500 text-center">{{ user.id }}</td>
                        <td class="p-4 font-bold text-gray-800">
                            {{ user.name }}
                            <span v-if="user.role === 'master'" class="ml-2 text-2xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded uppercase tracking-wider">Master</span>
                            <span v-else-if="user.role === 'admin'" class="ml-2 text-2xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded uppercase tracking-wider">Admin</span>
                        </td>
                        <td class="p-4 text-gray-500 font-medium">{{ user.email }}</td>
                        <td class="p-4">
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-700 text-xs font-semibold">{{ user.tier?.name || '없음' }}</span>
                        </td>
                        <td class="p-4 font-bold text-indigo-600">{{ user.current_points.toLocaleString() }} P</td>
                        <td class="p-4 text-center">
                            <span v-if="user.is_banned" class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">정지됨</span>
                            <span v-else class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">정상</span>
                        </td>
                        <td class="p-4 text-center">
                            <Link 
                                v-if="user.role !== 'master'"
                                :href="route('admin.users.edit', user.id)"
                                class="inline-block bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors mr-2 shadow-sm"
                            >
                                수정
                            </Link>

                            <button 
                                v-if="user.role !== 'master'"
                                @click="toggleBan(user)" 
                                :class="user.is_banned ? 'bg-gray-800 text-white hover:bg-gray-900' : 'bg-red-500 text-white hover:bg-red-600 shadow-sm'"
                                class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all focus:ring-2 focus:ring-offset-1 focus:ring-red-500 disabled:opacity-50"
                            >
                                {{ user.is_banned ? '정지 풀기' : '정지 먹이기' }}
                            </button>
                            <span v-else class="text-xs text-gray-400 italic">권한 없음</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Pagination UI -->
            <div class="p-5 border-t flex justify-between items-center text-sm font-medium text-gray-500 bg-gray-50">
                <div>검색된 대상: 총 {{ users.total }} 명</div>
                <div class="space-x-1.5 flex">
                    <Link v-for="(link, idx) in users.links" :key="idx" 
                          :href="link.url || '#'" 
                          v-html="link.label"
                          class="px-3 py-1.5 rounded-md border transition-colors"
                          :class="link.active ? 'bg-indigo-600 border-indigo-600 text-white font-bold shadow' : 'bg-white text-gray-700 hover:bg-gray-100 hover:text-indigo-600'"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
