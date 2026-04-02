<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    categories: Array,
});

const deleteCategory = (category) => {
    if (confirm(`'${category.name}' 카테고리를 정말 삭제하시겠습니까?\n(게시글이 연결되어 있으면 삭제 불가)`)) {
        router.delete(route('admin.categories.destroy', category.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="관리자 - 카테고리 관리" />

    <AdminLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">카테고리 관리</h1>
            <Link :href="route('admin.categories.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold shadow transition-colors">
                + 신규 카테고리 등록
            </Link>
        </div>

        <!-- Flash Messages -->
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
                        <th class="p-4 font-semibold w-16 text-center">순서 ID</th>
                        <th class="p-4 font-semibold">표시 이름</th>
                        <th class="p-4 font-semibold">경로 (Slug)</th>
                        <th class="p-4 font-semibold text-center">게시물 수</th>
                        <th class="p-4 font-semibold text-center">활성화</th>
                        <th class="p-4 font-semibold text-center">관리 액션</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50 text-sm transition-colors" :class="{'opacity-50': !cat.is_active}">
                        <td class="p-4 text-gray-500 text-center font-bold">{{ cat.sort_order }}</td>
                        <td class="p-4 font-bold text-gray-800">
                            {{ cat.name }}
                            <p class="text-xs text-gray-400 font-normal mt-0.5">{{ cat.description || '설명 없음' }}</p>
                        </td>
                        <td class="p-4 text-indigo-500 font-mono tracking-tighter">{{ cat.slug }}</td>
                        <td class="p-4 text-center font-bold text-gray-600">{{ cat.posts_count || 0 }}</td>
                        <td class="p-4 text-center">
                            <span v-if="cat.is_active" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">활성 ON</span>
                            <span v-else class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-xs font-bold">숨김 OFF</span>
                        </td>
                        <td class="p-4 text-center">
                            <Link 
                                :href="route('admin.categories.edit', cat.id)"
                                class="inline-block bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors mr-2 shadow-sm"
                            >
                                속성 수정
                            </Link>
                            <button 
                                @click="deleteCategory(cat)" 
                                class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm"
                            >
                                테이블 강제 삭제
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="categories.length === 0" class="text-center text-gray-400 py-10 font-medium">
                등록된 카테고리가 없습니다.
            </div>
        </div>
    </AdminLayout>
</template>
