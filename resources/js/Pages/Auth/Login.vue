<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status:           { type: String },
});

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="로그인" />

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">로그인</h2>
            <p class="text-sm text-gray-500 mt-2">대구 이야기, 다시 이어가요 🧡</p>
        </div>

        <!-- 상태 메시지 (비밀번호 재설정 등) -->
        <div v-if="status" class="mb-5 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm font-medium text-green-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- 이메일 -->
            <div>
                <InputLabel for="email" value="이메일 주소" class="text-gray-700 font-bold mb-1" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="이메일 주소를 입력해주세요"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <!-- 비밀번호 -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <InputLabel for="password" value="비밀번호" class="text-gray-700 font-bold" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-gray-400 hover:text-primary transition-colors font-medium"
                    >
                        비밀번호 찾기
                    </Link>
                </div>
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="비밀번호를 입력해주세요"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <!-- 로그인 유지 -->
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input
                    type="checkbox"
                    v-model="form.remember"
                    class="w-4 h-4 accent-primary rounded"
                />
                <span class="text-sm text-gray-600 font-medium">로그인 유지</span>
            </label>

            <!-- 로그인 버튼 -->
            <div class="pt-1">
                <button
                    type="submit"
                    class="w-full flex justify-center items-center px-4 py-3.5 rounded-full text-base font-black shadow-md transition-all active:scale-[0.98]"
                    :class="form.processing ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-primary hover:bg-orange-600 text-white'"
                    :disabled="form.processing"
                >
                    {{ form.processing ? '로그인 중...' : '로그인' }}
                </button>
            </div>

            <!-- 카카오 로그인 -->
            <div class="relative w-full">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-4 text-gray-500 font-bold">간편 로그인</span>
                </div>
            </div>

            <a
                :href="route('kakao.login')"
                class="flex w-full items-center justify-center gap-3 rounded-full bg-[#FEE500] px-4 py-3.5 text-base font-bold text-[#191919] shadow-md hover:bg-[#FADA0A] transition-all active:scale-[0.98]"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3c-4.97 0-9 3.185-9 7.115 0 2.558 1.707 4.8 4.27 6.054l-.841 3.08c-.05.187.159.333.31.233l3.638-2.423c.203.028.41.042.623.042 4.97 0 9-3.186 9-7.115S16.97 3 12 3z" />
                </svg>
                카카오로 로그인
            </a>

            <!-- 회원가입 링크 -->
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-600 font-medium pb-2">
                <span>아직 계정이 없으신가요?</span>
                <Link
                    :href="route('register')"
                    class="text-primary hover:text-orange-700 font-bold transition-colors"
                >
                    회원가입
                </Link>
            </div>

        </form>
    </GuestLayout>
</template>
