<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const props = defineProps({
    hasPassword: {
        type: Boolean,
        default: true,
    }
});

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    if (props.hasPassword) {
        nextTick(() => passwordInput.value.focus());
    }
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {
            if (props.hasPassword) {
                passwordInput.value.focus();
            }
        },
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-xl font-bold text-red-600">
                회원 탈퇴
            </h2>

            <div class="mt-2 space-y-3">
                <p class="text-sm text-gray-500 leading-relaxed break-keep">
                    탈퇴 시 계정은 즉시 비활성화되며, 관련 데이터는 서비스 운영 정책에 따라 일정 기간 보관 후 안전하게 파기됩니다.
                </p>
                <p class="text-sm text-gray-500 leading-relaxed break-keep">
                    삭제하기 전, 보관이 필요한 자료가 있다면 미리 확인해 주세요.<br class="hidden sm:block">
                    탈퇴 후에도 동일한 이메일로 언제든지 <span class="font-bold text-gray-700">재가입이 가능</span>합니다.
                </p>
            </div>
        </header>

        <button 
            @click="confirmUserDeletion"
            class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-5 py-2.5 rounded-full text-sm font-bold transition-all"
        >
            계정 탈퇴하기
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-8">
                <h2
                    class="text-xl font-bold text-gray-900"
                >
                    정말 계정을 탈퇴하시겠습니까?
                </h2>

                <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                    계정이 비활성화되면 서비스 이용이 제한되며, 등록된 정보는 정책에 따라 관리됩니다. <br>
                    <span v-if="hasPassword">안전한 처리를 위해 <b>현재 사용 중인 비밀번호</b>를 다시 한 번 입력해 주세요.</span>
                    <span v-else class="text-red-500 font-semibold">소셜 계정(비밀번호 없음)으로 가입하셨습니다. 탈퇴 버튼을 누르면 즉시 계정이 비활성화됩니다.</span>
                </p>

                <div class="mt-6" v-if="hasPassword">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm px-4 py-3 text-sm transition-all"
                        placeholder="비밀번호 입력"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button 
                        @click="closeModal"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-full text-sm font-bold transition-all"
                    >
                        취소
                    </button>

                    <button
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-full text-sm font-bold transition-all shadow-sm"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        네, 영구 탈퇴합니다
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
