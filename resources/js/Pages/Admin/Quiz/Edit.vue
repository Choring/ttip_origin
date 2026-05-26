<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    question: Object,
});

const form = useForm({
    dialect:     props.question.dialect,
    answer:      props.question.answer,
    wrong1:      props.question.wrong1,
    wrong2:      props.question.wrong2,
    explanation: props.question.explanation ?? '',
    is_active:   props.question.is_active,
});

const submit = () => {
    form.put(route('admin.quiz.update', props.question.id));
};
</script>

<template>
    <Head title="퀴즈 문제 편집 - Admin" />

    <AdminLayout>
        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.quiz.index')" class="text-gray-400 hover:text-gray-600 transition-colors">
                ← 목록으로
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 border-b-4 border-indigo-500 inline-block pb-1">퀴즈 문제 편집</h1>
        </div>

        <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form @submit.prevent="submit" class="space-y-6">

                <!-- 사투리 -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        사투리 <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.dialect"
                        type="text"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                        :class="{ 'border-red-400': form.errors.dialect }"
                    />
                    <p v-if="form.errors.dialect" class="mt-1 text-xs text-red-500">{{ form.errors.dialect }}</p>
                </div>

                <!-- 정답 -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        정답 <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.answer"
                        type="text"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                        :class="{ 'border-red-400': form.errors.answer }"
                    />
                    <p v-if="form.errors.answer" class="mt-1 text-xs text-red-500">{{ form.errors.answer }}</p>
                </div>

                <!-- 오답 -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            오답 1 <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.wrong1"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                            :class="{ 'border-red-400': form.errors.wrong1 }"
                        />
                        <p v-if="form.errors.wrong1" class="mt-1 text-xs text-red-500">{{ form.errors.wrong1 }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            오답 2 <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.wrong2"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                            :class="{ 'border-red-400': form.errors.wrong2 }"
                        />
                        <p v-if="form.errors.wrong2" class="mt-1 text-xs text-red-500">{{ form.errors.wrong2 }}</p>
                    </div>
                </div>

                <!-- 해설 -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">해설 (선택)</label>
                    <textarea
                        v-model="form.explanation"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 resize-none"
                    />
                </div>

                <!-- 활성화 -->
                <div class="flex items-center gap-3">
                    <input id="is_active" v-model="form.is_active" type="checkbox" class="w-4 h-4 text-indigo-600 rounded border-gray-300" />
                    <label for="is_active" class="text-sm font-bold text-gray-700">퀴즈에 활성화</label>
                </div>

                <!-- 버튼 -->
                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl transition-all disabled:opacity-50"
                    >
                        {{ form.processing ? '저장 중...' : '저장하기' }}
                    </button>
                    <Link :href="route('admin.quiz.index')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-6 py-2.5 rounded-xl transition-all">
                        취소
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
