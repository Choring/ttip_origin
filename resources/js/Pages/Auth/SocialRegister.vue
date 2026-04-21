<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    socialData: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.socialData.name || '',
    email: props.socialData.email || '',
});

const submit = () => {
    form.post(route('kakao.register'));
};
</script>

<template>
    <GuestLayout>
        <Head title="카카오 회원가입 완료" />

        <div class="text-center mb-8">
            <template v-if="socialData.avatar">
                <div class="relative inline-block mb-4">
                    <img :src="socialData.avatar" alt="Profile" class="w-24 h-24 rounded-full mx-auto border-4 border-white shadow-lg object-cover" />
                    <div class="absolute bottom-0 right-0 bg-[#FEE500] p-1.5 rounded-full shadow-sm border-2 border-white">
                        <svg class="h-4 w-4 text-[#191919]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 3c-4.97 0-9 3.185-9 7.115 0 2.558 1.707 4.8 4.27 6.054l-.841 3.08c-.05.187.159.333.31.233l3.638-2.423c.203.028.41.042.623.042 4.97 0 9-3.186 9-7.115S16.97 3 12 3z" />
                        </svg>
                    </div>
                </div>
            </template>
            <h2 class="text-2xl font-bold text-gray-800">반가워요, {{ socialData.name }}님!</h2>
            <p class="text-sm text-gray-500 mt-2">ttip 서비스 이용을 위해 이메일 정보를 입력해 주세요.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="name" value="활동하실 닉네임" class="text-gray-700 font-bold mb-1" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.name"
                    required
                    autofocus
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
                    placeholder="인증 및 알림을 받을 이메일"
                />

                <InputError class="mt-2" :message="form.errors.email" />
                <p class="mt-3 text-xs text-orange-600 bg-orange-50 p-3 rounded-lg border border-orange-100 italic">
                    💡 카카오 계정에서 이메일 정보를 가져올 수 없어 수동 입력이 필요합니다. 입력하신 이메일로 가입 완료 후 인증 메일이 발송됩니다.
                </p>
            </div>

            <div class="mt-8 pt-4">
                <button
                    type="submit"
                    class="w-full justify-center flex items-center bg-primary hover:bg-[#E65300] text-white px-4 py-4 rounded-full text-base font-bold shadow-md transition-all active:scale-[0.98]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? '가입 처리 중...' : '회원가입 완료하기' }}
                </button>
            </div>
            
            <div class="text-center pt-2">
                <button 
                    type="button" 
                    @click="$inertia.get(route('login'))"
                    class="text-sm text-gray-400 hover:text-gray-600 transition-colors underline decoration-dotted"
                >
                    가입 취소하고 로그인으로 돌아가기
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
