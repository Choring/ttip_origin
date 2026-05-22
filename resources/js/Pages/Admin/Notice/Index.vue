<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const debounce = (fn, delay = 300) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const props = defineProps({
    notices: { type: Object, default: () => ({ data: [], links: [], total: 0 }) },
    filters: { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters?.search || '');

watch(search, debounce(function (value) {
    router.get(route('admin.notices.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));


const togglePin = (id) => {
    router.patch(route('admin.notices.togglePin', id), {}, { preserveScroll: true });
};

const forceDelete = (id) => {
    if (confirm('정말로 이 공지사항을 삭제하시겠습니까?')) {
        router.delete(route('admin.notices.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="공지사항 관리 - Admin" />

    <AdminLayout>
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold flex items-center gap-2 shadow-sm animate-bounce">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ $page.props.flash.success }}
        </div>


        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 border-b-4 border-amber-500 inline-block pb-1">공지사항 관리</h1>
                <p class="mt-2 text-sm text-gray-600">서비스의 중요한 소식을 관리하고 상단 고정 여부를 설정합니다.</p>
            </div>
            <Link :href="route('admin.notices.create')" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <span>➕ 새 공지 작성</span>
            </Link>

        </div>


        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <div class="relative max-w-sm w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input v-model="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg font-medium bg-white placeholder-gray-500 focus:outline-none focus:border-amber-500 sm:text-sm" placeholder="공지 제목 검색...">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">고정</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">제목</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">조회수</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">작성일</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">관리</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="notice in notices.data" :key="notice.id" class="hover:bg-gray-50 transition duration-150 text-sm" :class="{'bg-amber-50/30': notice.is_pinned}">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">{{ notice.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button 
                                    @click="togglePin(notice.id)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all border"
                                    :class="notice.is_pinned 
                                        ? 'bg-amber-100 text-amber-700 border-amber-200 hover:bg-amber-200' 
                                        : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200'"
                                >
                                    {{ notice.is_pinned ? '📌 고정됨' : '📍 고정하기' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-bold truncate max-w-[300px]">{{ notice.title }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ notice.user?.name || '운영자' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">{{ notice.view_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-xs">{{ new Date(notice.created_at).toLocaleDateString() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold flex space-x-2 justify-center">
                                <Link :href="route('admin.notices.edit', notice.id)" class="text-gray-600 hover:text-indigo-600 font-bold px-2 py-1">
                                    편집
                                </Link>
                                <button type="button" @click="forceDelete(notice.id)" class="text-red-500 hover:text-red-700 font-bold px-2 py-1">
                                    삭제
                                </button>
                            </td>
                        </tr>
                        <tr v-if="notices.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium font-bold">등록된 공지사항이 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between sm:px-6">
                <div class="text-sm text-gray-500 font-medium">
                    총 <span class="font-bold text-gray-900">{{ notices.total }}</span> 개의 공지사항
                </div>
                <div class="flex flex-wrap gap-1">
                    <Link v-for="(link, i) in notices.links" :key="i" :href="link.url || '#'" class="px-3 py-1.5 border rounded-md text-xs transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600 font-bold shadow-sm' : (link.url ? 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300 font-medium' : 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed')" v-html="link.label"></Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
