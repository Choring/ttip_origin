<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    editUser: Object,
    tiers: Array
});

const form = useForm({
    name: props.editUser.name,
    email: props.editUser.email,
    password: '',
    password_confirmation: '',
    role: props.editUser.role,
    current_points: props.editUser.current_points,
    tier_id: props.editUser.tier_id || '',
});

const submit = () => {
    form.put(route('admin.users.update', props.editUser.id));
};
</script>

<template>
    <Head title="회원 정보 수정" />

    <AdminLayout>
        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">회원 정보 수정 ({{ editUser.id }}번 회원)</h1>
                <Link :href="route('admin.users.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    &larr; 목록으로 돌아가기
                </Link>
            </div>

            <!-- Error Flash -->
            <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl font-medium shadow-sm border border-red-100">
                ⚠️ {{ $page.props.flash.error }}
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 flex flex-col gap-6">
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-sm text-gray-500 font-medium">
                    가입일: {{ new Date(editUser.created_at).toLocaleString() }}
                </div>

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
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">비밀번호 무조건 강제 변경 (선택)</label>
                            <p class="text-xs text-gray-500 mb-2">기존 비밀번호를 유지하려면 아래 입력칸을 빈칸으로 남겨두세요.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <input v-model="form.password" type="password" placeholder="새 비밀번호 입력" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                                </div>
                                <div>
                                    <input v-model="form.password_confirmation" type="password" placeholder="새 비밀번호 확인" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2 border-t border-gray-100 pt-6 mt-2">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">서비스 조작 (어드민 영역)</h3>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">권한 등급 (Role)</label>
                            <select v-model="form.role" :disabled="editUser.role === 'master'" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-400">
                                <option value="user">일반 회원 (user)</option>
                                <option value="admin">부운영자 (admin)</option>
                                <option v-if="editUser.role === 'master'" value="master">최고 관리자 (master)</option>
                            </select>
                            <div v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</div>
                            <p v-if="editUser.role === 'master'" class="text-xs text-indigo-600 mt-1">※ 마스터(최고 관리자) 계정은 권한 강등이 불가능합니다.</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">강제 포인트 조작</label>
                            <input v-model="form.current_points" type="number" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <div v-if="form.errors.current_points" class="text-red-500 text-xs mt-1">{{ form.errors.current_points }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">계급(Tier) 수동 픽스 (선택)</label>
                            <select v-model="form.tier_id" class="w-full md:w-1/2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">(비워둠) 포인트량에 비례해 자동 동기화</option>
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
                            {{ form.processing ? '저장 중...' : '회원 정보 업데이트' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
