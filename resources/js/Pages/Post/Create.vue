<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const form = useForm({
    title: '',
    content: '',
});

const submit = () => {
    form.post(route('posts.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="새 글 쓰기" />

    <MainLayout>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold mb-6">새 글 쓰기</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">제목</label>
                    <input 
                        id="title" 
                        v-model="form.title" 
                        type="text" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="제목을 입력하세요"
                        required
                    />
                    <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">본문</label>
                    <textarea 
                        id="content" 
                        v-model="form.content" 
                        rows="12" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="마크다운이나 텍스트로 내용을 작성해주세요."
                        required
                    ></textarea>
                    <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                </div>

                <div class="flex justify-end pt-4 space-x-3">
                    <button 
                        type="button"
                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        @click="() => window.history.back()"
                    >
                        취소
                    </button>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? '작성 중...' : '작성하기' }}
                    </button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>
