<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    reports: Object,
    filters: Object,
});

const status = ref(props.filters?.status || 'all');
const type   = ref(props.filters?.type   || '');

const applyFilter = () => {
    router.get(route('admin.reports.index'), {
        status: status.value !== 'all' ? status.value : undefined,
        type:   type.value   || undefined,
    }, { preserveState: true, replace: true });
};

// 신고 처리 (resolve / dismiss)
const actionNote  = ref('');
const actionModal = ref(null); // { report, action }

const openAction = (report, action) => {
    actionNote.value = '';
    actionModal.value = { report, action };
};

const submitAction = () => {
    const { report, action } = actionModal.value;
    const routeName = action === 'resolve' ? 'admin.reports.resolve' : 'admin.reports.dismiss';
    router.patch(route(routeName, report.id), { admin_note: actionNote.value }, {
        preserveScroll: true,
        onSuccess: () => { actionModal.value = null; },
    });
};

const statusBadge = (s) => {
    if (s === 'pending')   return 'bg-yellow-100 text-yellow-700';
    if (s === 'resolved')  return 'bg-green-100 text-green-700';
    if (s === 'dismissed') return 'bg-gray-100 text-gray-500';
    return '';
};

const typeBadge = (t) => t === 'post' ? 'bg-indigo-100 text-indigo-700' : 'bg-purple-100 text-purple-700';
</script>

<template>
    <Head title="관리자 - 신고 관리" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">신고 관리</h1>
            <span class="text-sm text-gray-500">전체 {{ reports.total }}건</span>
        </div>

        <!-- 플래시 메시지 -->
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl font-medium shadow-sm border border-green-100">
            ✅ {{ $page.props.flash.success }}
        </div>

        <!-- 필터 -->
        <div class="mb-5 flex flex-wrap gap-3 items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold text-gray-600">상태</label>
                <select v-model="status" @change="applyFilter"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:border-indigo-400">
                    <option value="all">전체</option>
                    <option value="pending">처리 대기</option>
                    <option value="resolved">처리 완료</option>
                    <option value="dismissed">기각</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-semibold text-gray-600">유형</label>
                <select v-model="type" @change="applyFilter"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:border-indigo-400">
                    <option value="">전체</option>
                    <option value="post">게시글</option>
                    <option value="comment">댓글</option>
                </select>
            </div>
        </div>

        <!-- 신고 목록 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b uppercase tracking-wider">
                        <th class="p-4 font-semibold w-12 text-center">ID</th>
                        <th class="p-4 font-semibold w-20 text-center">유형</th>
                        <th class="p-4 font-semibold">신고 대상</th>
                        <th class="p-4 font-semibold w-28">신고자</th>
                        <th class="p-4 font-semibold w-24">사유</th>
                        <th class="p-4 font-semibold w-28 text-center">상태</th>
                        <th class="p-4 font-semibold w-40 text-center">관리</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="reports.data.length === 0">
                        <td colspan="7" class="p-10 text-center text-gray-400 text-sm">신고 내역이 없습니다.</td>
                    </tr>
                    <tr v-for="report in reports.data" :key="report.id"
                        class="hover:bg-gray-50 text-sm transition-colors">
                        <td class="p-4 text-gray-400 text-center text-xs">{{ report.id }}</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold" :class="typeBadge(report.reportable_label)">
                                {{ report.reportable_label === 'post' ? '게시글' : '댓글' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <a v-if="report.reportable_link" :href="report.reportable_link" target="_blank"
                                class="text-indigo-600 hover:underline font-medium line-clamp-1">
                                {{ report.reportable_title }}
                            </a>
                            <span v-else class="text-gray-400 italic text-xs">{{ report.reportable_title }}</span>
                            <p v-if="report.description" class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ report.description }}</p>
                        </td>
                        <td class="p-4 text-gray-700 font-medium text-xs">{{ report.user?.name || '-' }}</td>
                        <td class="p-4 text-gray-600 text-xs">{{ report.reason_label }}</td>
                        <td class="p-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold" :class="statusBadge(report.status)">
                                {{ report.status_label }}
                            </span>
                            <p v-if="report.admin_note" class="text-[10px] text-gray-400 mt-0.5 line-clamp-1">{{ report.admin_note }}</p>
                        </td>
                        <td class="p-4 text-center">
                            <div v-if="report.status === 'pending'" class="flex justify-center gap-2">
                                <button @click="openAction(report, 'resolve')"
                                    class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition">
                                    처리 완료
                                </button>
                                <button @click="openAction(report, 'dismiss')"
                                    class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-600 text-xs font-bold rounded-lg transition">
                                    기각
                                </button>
                            </div>
                            <span v-else class="text-xs text-gray-400">—</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 페이지네이션 -->
        <div v-if="reports.last_page > 1" class="mt-6 flex justify-center gap-1">
            <Link v-for="link in reports.links" :key="link.label"
                :href="link.url || '#'"
                v-html="link.label"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border"
                :class="link.active
                    ? 'bg-indigo-600 text-white border-indigo-600'
                    : link.url ? 'text-gray-600 border-gray-200 hover:bg-gray-50' : 'text-gray-300 border-gray-100 pointer-events-none'"
            />
        </div>
    </AdminLayout>

    <!-- 처리 확인 모달 -->
    <div v-if="actionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="actionModal = null">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4">
                {{ actionModal.action === 'resolve' ? '✅ 처리 완료로 변경' : '🚫 신고 기각' }}
            </h3>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600 mb-1">관리자 메모 <span class="text-gray-400 font-normal">(선택)</span></label>
                <textarea v-model="actionNote" rows="3" maxlength="500"
                    placeholder="처리 사유나 메모를 남길 수 있습니다..."
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button @click="actionModal = null"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-semibold transition">취소</button>
                <button @click="submitAction"
                    class="px-5 py-2 text-sm font-bold text-white rounded-lg transition"
                    :class="actionModal.action === 'resolve' ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600'">
                    확인
                </button>
            </div>
        </div>
    </div>
</template>
