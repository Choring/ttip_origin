<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    isKakaoLinked: {
        type: Boolean,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-bold text-gray-900">
                기본 정보
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                사용하실 닉네임과 연락받을 이메일 주소를 변경할 수 있습니다.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="닉네임" class="font-bold text-gray-700 mb-1" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="이메일 주소" class="font-bold text-gray-700 mb-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    이메일 인증이 완료되지 않았습니다.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-primary underline hover:text-orange-700 focus:outline-none"
                    >
                        인증 메일을 다시 보내려면 여기를 클릭하세요.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    새로운 인증 링크가 이메일로 전송되었습니다.
                </div>
            </div>

            <!-- 카카오 연동 섹션 -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">소셜 계정 연동</h3>
                        <p class="text-xs text-gray-500 mt-0.5">카카오 계정을 연동하여 간편하게 로그인하세요.</p>
                    </div>
                    <div>
                        <template v-if="isKakaoLinked">
                            <div class="flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-full border border-gray-100 shadow-sm">
                                <svg class="h-4 w-4 text-[#FEE500]" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 3c-4.97 0-9 3.185-9 7.115 0 2.558 1.707 4.8 4.27 6.054l-.841 3.08c-.05.187.159.333.31.233l3.638-2.423c.203.028.41.042.623.042 4.97 0 9-3.186 9-7.115S16.97 3 12 3z" />
                                </svg>
                                <span class="text-xs font-bold text-gray-600">연동됨</span>
                            </div>
                        </template>
                        <template v-else>
                            <a 
                                :href="route('kakao.login')"
                                class="flex items-center gap-2 bg-[#FEE500] hover:bg-[#FADA0A] px-4 py-2.5 rounded-full shadow-sm transition-all active:scale-[0.98]"
                            >
                                <svg class="h-4 w-4 text-[#191919]" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 3c-4.97 0-9 3.185-9 7.115 0 2.558 1.707 4.8 4.27 6.054l-.841 3.08c-.05.187.159.333.31.233l3.638-2.423c.203.028.41.042.623.042 4.97 0 9-3.186 9-7.115S16.97 3 12 3z" />
                                </svg>
                                <span class="text-xs font-bold text-[#191919]">카카오 연동하기</span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button
                    type="submit"
                    class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-full text-sm font-bold shadow-sm transition-all active:scale-[0.98]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    저장하기
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm font-bold text-primary"
                    >
                        저장되었습니다.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
