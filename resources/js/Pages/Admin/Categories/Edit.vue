<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category.name,
    slug: props.category.slug,
    description: props.category.description || '',
    sort_order: props.category.sort_order,
    is_active: Boolean(props.category.is_active),
});

const submit = () => {
    form.put(route('admin.categories.update', props.category.id));
};
</script>

<template>
    <Head title="카테고리 수정" />

    <AdminLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">카테고리 설정 업데이트 지부</h1>
                <Link :href="route('admin.categories.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    &larr; 목록으로 복귀
                </Link>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">화면 노출 이름 (Name)</label>
                        <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">단독 URL 식별자 (Slug) <span class="text-xs text-red-500 ml-1">경로가 파손될 수 있으니 수정 시 주의!</span></label>
                        <input v-model="form.slug" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-indigo-500" required>
                        <div v-if="form.errors.slug" class="text-red-500 text-xs mt-1">{{ form.errors.slug }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">카테고리 설명 텍스트 (Description)</label>
                        <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">프론트엔드 배치 순서 (정렬 번호)</label>
                            <input v-model="form.sort_order" type="number" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        
                        <div class="flex items-center pt-8">
                            <label class="flex items-center cursor-pointer">
                                <span class="text-sm font-bold text-gray-700 mr-3">사이트 활성화 여부 (ON/OFF)</span>
                                <input v-model="form.is_active" type="checkbox" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer bg-gray-100 border-gray-300">
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100 space-x-3 mt-4">
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors disabled:opacity-50 shadow-md">
                            {{ form.processing ? '저장 중...' : '변경사항 저장하기' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
