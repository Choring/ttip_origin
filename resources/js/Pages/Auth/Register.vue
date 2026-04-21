<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="회원가입" />

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">회원가입</h2>
            <p class="text-sm text-gray-500 mt-2">ttip의 회원이 되어 여러분의 팁을 나눠주세요!</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="닉네임" class="text-gray-700 font-bold mb-1" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="사용하실 닉네임을 적어주세요"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="이메일 주소" class="text-gray-700 font-bold mb-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="로그인 시 사용될 이메일"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="비밀번호" class="text-gray-700 font-bold mb-1" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="영문, 숫자, 특수문자 조합 8자 이상"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="비밀번호 확인"
                    class="text-gray-700 font-bold mb-1"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="비밀번호를 한번 더 입력해주세요"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-8 pt-2">
                <button
                    type="submit"
                    class="w-full justify-center flex items-center bg-primary hover:bg-[#E65300] text-white px-4 py-3.5 rounded-full text-base font-bold shadow-md transition-all active:scale-[0.98]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? '가입 중...' : '회원가입 완료' }}
                </button>
            </div>

            <div class="mt-8">
                <div class="relative w-full">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-[#F9FAFB] px-4 text-gray-500 font-bold">간편 회원가입</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a
                        :href="route('kakao.login')"
                        class="flex w-full items-center justify-center gap-3 rounded-full bg-[#FEE500] px-4 py-3.5 text-base font-bold text-[#191919] shadow-md hover:bg-[#FADA0A] transition-all active:scale-[0.98]"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 3c-4.97 0-9 3.185-9 7.115 0 2.558 1.707 4.8 4.27 6.054l-.841 3.08c-.05.187.159.333.31.233l3.638-2.423c.203.028.41.042.623.042 4.97 0 9-3.186 9-7.115S16.97 3 12 3z" />
                        </svg>
                        <span>카카오로 시작하기</span>
                    </a>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-center space-x-2 text-sm text-gray-600 font-medium pb-2">
                <span>이미 계정이 있으신가요?</span>
                <Link
                    :href="route('login')"
                    class="text-primary hover:text-orange-700 font-bold transition-colors focus:outline-none"
                >
                    로그인하기
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
