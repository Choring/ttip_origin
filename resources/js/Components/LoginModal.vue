<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
        onError: () => {
            // Error handling by Inertia
        },
        onFinish: () => form.reset('password'),
    });
};

const close = () => {
    emit('close');
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <Modal :show="show" @close="close" max-width="md">
        <div class="p-8 sm:p-10 relative">
            <!-- 닫기 버튼 -->
            <button @click="close" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-full hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- 로고 및 환영문구 -->
            <div class="text-center mb-8">
                <div class="inline-block mb-2">
                    <span class="text-primary font-extrabold text-4xl tracking-tighter cursor-default">ttip</span>
                </div>
                <h2 class="text-xl font-bold text-gray-800">다시 오신 것을 환영해요!</h2>
                <p class="text-sm text-gray-500 mt-2">오늘도 유용한 팁을 나누어 볼까요?</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
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
                        placeholder="이메일을 적어주세요"
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
                        autocomplete="current-password"
                        placeholder="비밀번호"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <label class="flex items-center cursor-pointer">
                        <Checkbox 
                            name="remember" 
                            v-model:checked="form.remember" 
                            class="text-primary focus:ring-primary rounded border-gray-300 shadow-sm"
                        />
                        <span class="ms-2 text-sm font-medium text-gray-600">로그인 유지</span>
                    </label>
                    <Link
                        :href="route('password.request')"
                        class="text-sm font-semibold text-primary hover:text-orange-700 transition-colors focus:outline-none"
                    >
                        비밀번호 찾기
                    </Link>
                </div>

                <div class="mt-8 pt-2">
                    <button
                        type="submit"
                        class="w-full justify-center flex items-center bg-primary hover:bg-[#E65300] text-white px-4 py-3.5 rounded-full text-base font-bold shadow-md transition-all active:scale-[0.98]"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? '로그인 중...' : '로그인' }}
                    </button>
                    
                    <div class="text-center mt-6 text-sm text-gray-600 font-medium">
                        아직 회원이 아니신가요?
                        <Link
                            :href="route('register')"
                            class="text-primary hover:text-orange-700 font-bold ml-1 transition-colors"
                        >
                            회원가입
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </Modal>
</template>
