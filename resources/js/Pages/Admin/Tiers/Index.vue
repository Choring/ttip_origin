<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tiers: Array,
});

// ── 수정 모달 ──────────────────────────────────────────────────────
const editModal = ref(null); // { ...tier }

const openEdit = (tier) => {
    editModal.value = { ...tier };
};

const submitEdit = () => {
    router.put(route('admin.tiers.update', editModal.value.id), {
        name:       editModal.value.name,
        min_points: editModal.value.min_points,
        icon_url:   editModal.value.icon_url,
    }, {
        preserveScroll: true,
        onSuccess: () => { editModal.value = null; },
    });
};

// ── 추가 모달 ──────────────────────────────────────────────────────
const showAddModal = ref(false);
const newTier = ref({ name: '', min_points: '', icon_url: '' });

const openAdd = () => {
    newTier.value = { name: '', min_points: '', icon_url: '' };
    showAddModal.value = true;
};

const submitAdd = () => {
    router.post(route('admin.tiers.store'), newTier.value, {
        preserveScroll: true,
        onSuccess: () => { showAddModal.value = false; },
    });
};

// ── 삭제 ──────────────────────────────────────────────────────────
const deleteTier = (tier) => {
    if (tier.users_count > 0) {
        alert(`현재 ${tier.users_count}명이 이 티어를 사용 중입니다. 삭제할 수 없습니다.`);
        return;
    }
    if (confirm(`'${tier.name}' 티어를 삭제하시겠습니까?`)) {
        router.delete(route('admin.tiers.destroy', tier.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="관리자 - 티어 관리" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">티어 관리</h1>
                <p class="text-sm text-gray-500 mt-1">누적 포인트 기준으로 유저 등급을 설정합니다. 변경 시 전체 유저 티어가 자동 재계산됩니다.</p>
            </div>
            <button @click="openAdd"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold shadow transition-colors text-sm">
                + 티어 추가
            </button>
        </div>

        <!-- 플래시 메시지 -->
        <div v-if="$page.props.flash?.success" class="mb-5 p-4 bg-green-50 text-green-700 rounded-xl font-medium border border-green-100">
            ✅ {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-5 p-4 bg-red-50 text-red-700 rounded-xl font-medium border border-red-100">
            ⚠️ {{ $page.props.flash.error }}
        </div>

        <!-- 안내 박스 -->
        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-sm text-indigo-700">
            <strong>💡 동작 방식:</strong>
            티어는 유저의 <strong>누적 획득 포인트(total_points)</strong> 기준으로 자동 배정됩니다.
            포인트를 소비해도 티어는 유지됩니다. 티어 기준을 수정하면 <strong>전체 유저의 티어가 즉시 재계산</strong>됩니다.
        </div>

        <!-- 티어 목록 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs border-b uppercase tracking-wider">
                        <th class="p-4 font-semibold w-16 text-center">순서</th>
                        <th class="p-4 font-semibold w-20 text-center">아이콘</th>
                        <th class="p-4 font-semibold">티어명</th>
                        <th class="p-4 font-semibold">필요 누적 포인트</th>
                        <th class="p-4 font-semibold text-center">현재 유저 수</th>
                        <th class="p-4 font-semibold text-center w-36">관리</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="tiers.length === 0">
                        <td colspan="6" class="p-10 text-center text-gray-400 text-sm">등록된 티어가 없습니다.</td>
                    </tr>
                    <tr v-for="(tier, index) in tiers" :key="tier.id"
                        class="hover:bg-gray-50 transition-colors text-sm">
                        <td class="p-4 text-center text-gray-400 font-bold">{{ index + 1 }}</td>
                        <td class="p-4 text-center text-2xl">{{ tier.icon_url || '—' }}</td>
                        <td class="p-4 font-bold text-gray-800">{{ tier.name }}</td>
                        <td class="p-4">
                            <span class="font-bold text-indigo-600">{{ tier.min_points.toLocaleString() }}</span>
                            <span class="text-gray-400 ml-1">P 이상</span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold"
                                :class="tier.users_count > 0 ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-400'">
                                {{ tier.users_count.toLocaleString() }}명
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button @click="openEdit(tier)"
                                    class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-bold rounded-lg transition">
                                    수정
                                </button>
                                <button @click="deleteTier(tier)"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition"
                                    :class="tier.users_count > 0
                                        ? 'bg-gray-100 text-gray-300 cursor-not-allowed'
                                        : 'bg-red-50 hover:bg-red-100 text-red-500'">
                                    삭제
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 포인트 구조 안내 -->
        <div class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-black text-gray-700 mb-3">현재 포인트 지급 기준</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs text-gray-600">
                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">✍️ 게시글 작성 <strong class="text-indigo-600 ml-auto">+50P</strong></div>
                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">👍 띱(좋아요) 받음 <strong class="text-indigo-600 ml-auto">+5P</strong></div>
                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">📩 내 글에 댓글 달림 <strong class="text-indigo-600 ml-auto">+3P</strong></div>
                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">💬 댓글 작성(10자+) <strong class="text-indigo-600 ml-auto">+1P</strong></div>
                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">❤️ 댓글 좋아요 받음 <strong class="text-indigo-600 ml-auto">+5P</strong></div>
            </div>
        </div>

    </AdminLayout>

    <!-- 수정 모달 -->
    <div v-if="editModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="editModal = null">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5">✏️ 티어 수정</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">아이콘 (이모지)</label>
                    <input v-model="editModal.icon_url" type="text" maxlength="10" placeholder="🌱"
                        class="w-full px-3 py-2 text-2xl border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 text-center" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">티어명 <span class="text-red-400">*</span></label>
                    <input v-model="editModal.name" type="text" maxlength="20" placeholder="씨앗"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">필요 누적 포인트 <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input v-model.number="editModal.min_points" type="number" min="0" placeholder="0"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 pr-8" />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-bold">P</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">이 값 이상의 누적 포인트를 가진 유저에게 배정됩니다.</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button @click="editModal = null"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 font-semibold transition">취소</button>
                <button @click="submitEdit"
                    :disabled="!editModal.name || editModal.min_points === ''"
                    class="px-5 py-2 text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition disabled:opacity-50">
                    저장
                </button>
            </div>
        </div>
    </div>

    <!-- 추가 모달 -->
    <div v-if="showAddModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
        @click.self="showAddModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5">➕ 새 티어 추가</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">아이콘 (이모지)</label>
                    <input v-model="newTier.icon_url" type="text" maxlength="10" placeholder="🌟"
                        class="w-full px-3 py-2 text-2xl border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 text-center" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">티어명 <span class="text-red-400">*</span></label>
                    <input v-model="newTier.name" type="text" maxlength="20" placeholder="다이아"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">필요 누적 포인트 <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input v-model.number="newTier.min_points" type="number" min="0" placeholder="50000"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 pr-8" />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-bold">P</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button @click="showAddModal = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 font-semibold transition">취소</button>
                <button @click="submitAdd"
                    :disabled="!newTier.name || newTier.min_points === ''"
                    class="px-5 py-2 text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition disabled:opacity-50">
                    추가
                </button>
            </div>
        </div>
    </div>
</template>
