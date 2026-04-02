<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tiers: Array
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    current_points: 0,
    tier_id: '',
});

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <Head title="회원 수동 등록" />

    <AdminLayout>
        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">새 회원 등록</h1>
                <Link :href="route('admin.users.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    &larr; 목록으로 돌아가기
                </Link>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">이름</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">이메일</label>
                            <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">비밀번호</label>
                            <input v-model="form.password" type="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">비밀번호 확인</label>
                            <input v-model="form.password_confirmation" type="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        
                        <div class="md:col-span-2 border-t border-gray-100 pt-6 mt-2">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">서비스 정보 설정</h3>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">권한 (Role)</label>
                            <select v-model="form.role" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="user">일반 유저 (user)</option>
                                <option value="admin">부관리자 (admin)</option>
                            </select>
                            <div v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">초기 제공 포인트</label>
                            <input v-model="form.current_points" type="number" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <div v-if="form.errors.current_points" class="text-red-500 text-xs mt-1">{{ form.errors.current_points }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">초기 티어 강제 조작 (선택)</label>
                            <select v-model="form.tier_id" class="w-full md:w-1/2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">(자동판단) 포인트 기반 자동 승급</option>
                                <option v-for="tier in tiers" :key="tier.id" :value="tier.id">
                                    {{ tier.name }} (조건: {{ tier.required_points }}P)
                                </option>
                            </select>
                            <div v-if="form.errors.tier_id" class="text-red-500 text-xs mt-1">{{ form.errors.tier_id }}</div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100 space-x-3">
                        <Link :href="route('admin.users.index')" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition-colors">취소</Link>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors disabled:opacity-50 shadow-md">
                            {{ form.processing ? '등록 중...' : '회원 등록 완료' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
