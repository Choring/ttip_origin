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
    questions: { type: Object, default: () => ({ data: [], links: [], total: 0 }) },
    filters:   { type: Object, default: () => ({ search: '' }) },
});

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get(route('admin.quiz.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const toggleActive = (id) => {
    router.patch(route('admin.quiz.toggleActive', id), {}, { preserveScroll: true });
};

const destroy = (id) => {
    if (confirm('정말로 이 문제를 삭제하시겠습니까?')) {
        router.delete(route('admin.quiz.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="퀴즈 문제 관리 - Admin" />

    <AdminLayout>
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $page.props.flash.success }}
        </div>

        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 border-b-4 border-indigo-500 inline-block pb-1">퀴즈 문제 관리</h1>
                <p class="mt-2 text-sm text-gray-600">대구 사투리 퀴즈 문제를 추가/수정/삭제합니다.</p>
            </div>
            <Link :href="route('admin.quiz.create')" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center gap-2">
                ➕ 문제 추가
            </Link>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- 검색 -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="relative max-w-sm w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                    </div>
                    <input v-model="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white placeholder-gray-500 focus:outline-none focus:border-indigo-500 sm:text-sm" placeholder="사투리 또는 정답 검색...">
                </div>
            </div>

            <!-- 테이블 -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">사투리</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">정답</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">오답 1</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">오답 2</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">활성</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">관리</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="q in questions.data" :key="q.id" class="hover:bg-gray-50 transition duration-150 text-sm" :class="{ 'opacity-50': !q.is_active }">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">{{ q.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-900 text-base">"{{ q.dialect }}"</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-green-700 font-bold">{{ q.answer }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ q.wrong1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ q.wrong2 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button
                                    @click="toggleActive(q.id)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all border"
                                    :class="q.is_active
                                        ? 'bg-green-100 text-green-700 border-green-200 hover:bg-green-200'
                                        : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200'"
                                >
                                    {{ q.is_active ? '✅ 활성' : '⏸ 비활성' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center flex gap-2 justify-center">
                                <Link :href="route('admin.quiz.edit', q.id)" class="text-gray-600 hover:text-indigo-600 font-bold px-2 py-1">
                                    편집
                                </Link>
                                <button type="button" @click="destroy(q.id)" class="text-red-500 hover:text-red-700 font-bold px-2 py-1">
                                    삭제
                                </button>
                            </td>
                        </tr>
                        <tr v-if="questions.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 font-medium">등록된 퀴즈 문제가 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    총 <span class="font-bold text-gray-900">{{ questions.total }}</span> 개의 문제
                </div>
                <div class="flex flex-wrap gap-1">
                    <Link v-for="(link, i) in questions.links" :key="i" :href="link.url || '#'"
                        class="px-3 py-1.5 border rounded-md text-xs transition-colors"
                        :class="link.active
                            ? 'bg-indigo-600 text-white border-indigo-600 font-bold'
                            : (link.url ? 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300' : 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed')"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
