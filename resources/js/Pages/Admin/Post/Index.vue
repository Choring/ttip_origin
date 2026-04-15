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
    posts: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, debounce(function (value) {
    router.get(route('admin.posts.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const forceDelete = (id) => {
    if (confirm('정말로 이 게시글을 강제 삭제하시겠습니까? 돌이킬 수 없으며 원 작성자에게 통보되지 않습니다.')) {
        router.delete(route('admin.posts.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="게시글 관리" />

    <AdminLayout>
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 border-b-4 border-indigo-600 inline-block pb-1">게시글 통합 관리</h1>
                <p class="mt-2 text-sm text-gray-600">모든 회원의 게시글 현황을 모니터링하고 중재합니다.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <div class="relative max-w-sm w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input v-model="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 sm:text-sm" placeholder="제목 또는 작성자 검색...">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">상태</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">제목 & 태그</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">작성자</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">조회수</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">작성일</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">관리옵션</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50 transition duration-150 text-sm">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">{{ post.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold w-fit">{{ post.category?.name || '미분류' }}</span>
                                    <span v-if="post.type === 'pinned'" class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black w-fit">📌 상단고정</span>
                                    <span v-else-if="post.type === 'notice'" class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-[10px] font-black w-fit">📢 공지</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-bold truncate max-w-[200px]">{{ post.title }}</div>
                                <div class="text-xs text-gray-500 flex gap-1 mt-1" v-if="post.tags && post.tags.length">
                                    <span v-for="t in post.tags" :key="t">#{{ t }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ post.user?.name || '탈퇴/알수없음' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ post.view_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ new Date(post.created_at).toLocaleDateString() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold flex space-x-2 justify-center">
                                <Link :href="route('admin.posts.edit', post.id)" class="text-indigo-600 hover:text-white hover:bg-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-md transition-colors border border-indigo-100">
                                    조회/수정
                                </Link>
                                <button type="button" @click="forceDelete(post.id)" class="text-red-600 hover:text-white hover:bg-red-600 bg-red-50 px-3 py-1.5 rounded-md transition-colors border border-red-100">
                                    강제삭제
                                </button>
                            </td>
                        </tr>
                        <tr v-if="posts.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 font-medium">검색된 게시글이 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between sm:px-6">
                <div>
                    <span class="text-sm text-gray-700">
                        총 <span class="font-bold">{{ posts.total }}</span> 개의 게시물
                    </span>
                </div>
                <div class="flex flex-wrap gap-1">
                    <Link v-for="(link, i) in posts.links" :key="i" :href="link.url || '#'" class="px-3 py-1.5 border rounded-md text-sm transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600 font-bold shadow-sm' : (link.url ? 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300 font-medium' : 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed')" v-html="link.label"></Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
