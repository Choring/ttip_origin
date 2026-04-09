<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
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

            <p class="mt-1 text-sm text-gray-500">
                탈퇴하시면 계정과 관련된 모든 개인정보와 데이터가 영구적으로 삭제되며 다시 복구할 수 없습니다.<br>
                삭제하기 전, 보관이 필요한 자료가 있다면 미리 백업해 주세요.
            </p>
        </header>

        <button 
            @click="confirmUserDeletion"
            class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-5 py-2.5 rounded-full text-sm font-bold transition-all"
        >
            계정 삭제하기
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-8">
                <h2
                    class="text-xl font-bold text-gray-900"
                >
                    정말 계정을 탈퇴하시겠습니까?
                </h2>

                <p class="mt-3 text-sm text-gray-600">
                    계정이 삭제되면 모든 리소스와 데이터가 영구적으로 지워집니다. <br>
                    안전한 처리를 위해 <b>현재 사용 중인 비밀번호</b>를 다시 한 번 입력해 주세요.
                </p>

                <div class="mt-6">
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
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-full text-sm font-bold transition-all"
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
