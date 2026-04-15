<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="이메일 인증" />

        <div class="text-center mb-6">
            <div class="text-5xl mb-4">📧</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">이메일을 확인해 주세요!</h2>
            <p class="text-sm text-gray-500 leading-relaxed">
                가입하신 이메일 주소로 인증 링크를 발송했어요.<br>
                메일함을 확인하고 링크를 클릭하면 인증이 완료됩니다.
            </p>
        </div>

        <div
            class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm font-medium text-green-700 text-center"
            v-if="verificationLinkSent"
        >
            ✅ 인증 메일을 재발송했어요. 메일함을 확인해 주세요.
        </div>

        <form @submit.prevent="submit" class="space-y-3">
            <button
                type="submit"
                class="w-full flex justify-center items-center bg-primary hover:bg-[#E65300] text-white px-4 py-3.5 rounded-full text-base font-bold shadow-md transition-all active:scale-[0.98]"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                {{ form.processing ? '발송 중...' : '인증 메일 재발송' }}
            </button>

            <div class="text-center">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
                >
                    로그아웃
                </Link>
            </div>
        </form>

        <p class="mt-6 text-xs text-gray-400 text-center">
            메일이 오지 않는다면 스팸함도 확인해 주세요.
        </p>
    </GuestLayout>
</template>
