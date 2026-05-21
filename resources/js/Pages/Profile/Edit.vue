<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

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

const page = usePage();
const user = page.props.auth.user;
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

                <!-- 포인트 / 티어 현황 카드 -->
                <Link :href="route('profile.points')"
                    class="block bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 rounded-3xl p-5 shadow-md transition-all group">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">{{ user?.tier?.icon_url || '🌱' }}</span>
                            <div>
                                <p class="text-xs text-indigo-200 font-semibold">현재 티어</p>
                                <p class="text-lg font-black">{{ user?.tier?.name || '씨앗' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-indigo-200 font-semibold">보유 포인트</p>
                            <p class="text-2xl font-black">{{ (user?.current_points || 0).toLocaleString() }}<span class="text-sm ml-0.5">P</span></p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-1 text-indigo-200 text-xs font-semibold group-hover:text-white transition-colors">
                        포인트 내역 보기 →
                    </div>
                </Link>

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
