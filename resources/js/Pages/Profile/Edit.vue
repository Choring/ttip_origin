<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    hasPassword: {
        type: Boolean,
    },
    isKakaoLinked: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="내 프로필 관리" />

    <MainLayout>
        <div class="py-2 sm:py-6 text-left">
            <div class="mb-6 px-4">
                <h2 class="text-2xl font-extrabold text-gray-800">
                    마이페이지
                </h2>
                <p class="text-sm text-gray-500 mt-1">계정 정보 변경 및 보안 설정을 관리하세요.</p>
            </div>

            <div class="space-y-6 px-2 sm:px-0">
                <div class="bg-white p-6 shadow-md rounded-3xl border border-gray-50 sm:p-8 transition-shadow">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :is-kakao-linked="isKakaoLinked"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-6 shadow-md rounded-3xl border border-gray-50 sm:p-8 transition-shadow">
                    <UpdatePasswordForm 
                        :has-password="hasPassword"
                        class="max-w-xl" 
                    />
                </div>

                <div class="bg-white p-6 shadow-md rounded-3xl border border-gray-50 sm:p-8 transition-shadow">
                    <DeleteUserForm 
                        :has-password="hasPassword"
                        class="max-w-xl" 
                    />
                </div>
            </div>
        </div>
    </MainLayout>
</template>
