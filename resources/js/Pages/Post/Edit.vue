<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    post: Object,
    categories: Array
});

const form = useForm({
    category_id: props.post.category_id || '',
    title: props.post.title || '',
    content: props.post.content || '',
});

const submit = () => {
    form.put(route('posts.update', props.post.id));
};
</script>

<template>
    <Head title="게시글 수정" />

    <MainLayout>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold mb-6">게시글 수정</h2>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="category_id" class="block text-sm font-bold text-gray-700 mb-2">카테고리</label>
                    <select 
                        id="category_id" 
                        v-model="form.category_id" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        required
                    >
                        <option value="" disabled>분류를 선택하세요</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-2">제목</label>
                    <input 
                        id="title" 
                        v-model="form.title" 
                        type="text" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="제목을 입력하세요"
                        required
                    />
                    <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-bold text-gray-700 mb-2">본문</label>
                    <textarea 
                        id="content" 
                        v-model="form.content" 
                        rows="12" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="마크다운이나 텍스트로 내용을 수정해주세요."
                        required
                    ></textarea>
                    <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                </div>

                <!-- Note: Adding images inside editing is omitted for simplicity of updating metadata only -->

                <div class="flex justify-end pt-4 space-x-3">
                    <Link 
                        :href="route('posts.show', post.id)"
                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        취소
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? '저장 중...' : '변경사항 저장하기' }}
                    </button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>
