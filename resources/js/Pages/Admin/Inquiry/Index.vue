<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    inquiries:    { type: Object, required: true },
    pendingCount: { type: Number, default: 0 },
    filters:      { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const applyFilter = () => {
    router.get(route('admin.inquiries.index'), {
        search: search.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('ko-KR', { year: 'numeric', month: '2-digit', day: '2-digit' }) : '';
</script>

<template>
    <Head title="문의 관리" />
    <AdminLayout>
        <div class="space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">문의 관리</h1>
                    <p v-if="pendingCount > 0" class="text-sm text-orange-500 font-bold mt-0.5">
                        미답변 문의 {{ pendingCount }}건
                    </p>
                </div>
            </div>

            <!-- 필터 -->
            <div class="flex flex-wrap gap-3">
                <input v-model="search" type="text" placeholder="제목, 이름, 이메일 검색..."
                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 w-64"
                    @keyup.enter="applyFilter" />
                <select v-model="status" @change="applyFilter"
                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">전체</option>
                    <option value="pending">미답변</option>
                    <option value="answered">답변완료</option>
                </select>
                <button @click="applyFilter"
                    class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-bold hover:bg-orange-600 transition-colors">
                    검색
                </button>
            </div>

            <!-- 테이블 -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 font-bold text-gray-500 text-xs uppercase tracking-wider">상태</th>
                            <th class="text-left px-5 py-3 font-bold text-gray-500 text-xs uppercase tracking-wider">제목</th>
                            <th class="text-left px-5 py-3 font-bold text-gray-500 text-xs uppercase tracking-wider">작성자</th>
                            <th class="text-left px-5 py-3 font-bold text-gray-500 text-xs uppercase tracking-wider">이메일</th>
                            <th class="text-left px-5 py-3 font-bold text-gray-500 text-xs uppercase tracking-wider">접수일</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="inquiries.data.length === 0">
                            <td colspan="5" class="text-center py-12 text-gray-400">문의가 없습니다.</td>
                        </tr>
                        <tr v-for="inq in inquiries.data" :key="inq.id"
                            class="hover:bg-gray-50 transition-colors cursor-pointer"
                            @click="router.visit(route('admin.inquiries.show', inq.id))">
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black"
                                    :class="inq.status === 'pending'
                                        ? 'bg-orange-100 text-orange-700'
                                        : 'bg-green-100 text-green-700'">
                                    <span v-if="inq.status === 'pending'" class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                    {{ inq.status === 'pending' ? '미답변' : '답변완료' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-gray-900 max-w-xs truncate">{{ inq.subject }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ inq.name }}</td>
                            <td class="px-5 py-3.5 text-gray-400 text-xs">{{ inq.email }}</td>
                            <td class="px-5 py-3.5 text-gray-400 text-xs">{{ formatDate(inq.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div v-if="inquiries.last_page > 1" class="flex justify-center gap-1">
                <Link v-for="link in inquiries.links" :key="link.label"
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="px-3 py-1.5 rounded-lg text-sm font-bold transition-colors"
                    :class="link.active
                        ? 'bg-orange-500 text-white'
                        : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300 cursor-default'"
                />
            </div>

        </div>
    </AdminLayout>
</template>
