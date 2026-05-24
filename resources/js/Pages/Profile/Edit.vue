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

                <!-- 내 활동 (내가 쓴 글 / 북마크) -->
                <div class="grid grid-cols-2 gap-3">
                    <Link
                        :href="route('profile.activity', { tab: 'posts' })"
                        class="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-4 shadow-sm hover:border-indigo-200 hover:shadow-md transition-all group"
                    >
                        <span class="text-2xl">✏️</span>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold">내가 쓴 글</p>
                            <p class="text-sm font-black text-gray-800 group-hover:text-indigo-600 transition-colors">게시글 보기 →</p>
                        </div>
                    </Link>
                    <Link
                        :href="route('profile.activity', { tab: 'bookmarks' })"
                        class="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-4 shadow-sm hover:border-amber-200 hover:shadow-md transition-all group"
                    >
                        <span class="text-2xl">🔖</span>
                        <div>
                            <p class="text-xs text-gray-400 font-semibold">북마크</p>
                            <p class="text-sm font-black text-gray-800 group-hover:text-amber-500 transition-colors">저장 목록 →</p>
                        </div>
                    </Link>
                </div>

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
