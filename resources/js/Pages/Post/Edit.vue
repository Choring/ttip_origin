<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    post: Object,
    categories: Array
});

import { ref } from 'vue';

const form = useForm({
    category_id: props.post.category_id || '',
    title: props.post.title || '',
    content: props.post.content || '',
    tags: props.post.tags || [],
    type: props.post.type || 'general',
    is_pinned: props.post.is_pinned || false,
});

const tagInput = ref('');
const addTag = () => {
    const val = tagInput.value.trim();
    if (val && !form.tags.includes(val) && form.tags.length < 3) {
        form.tags.push(val);
    }
    tagInput.value = '';
};
const removeTag = (index) => {
    form.tags.splice(index, 1);
};

const submit = () => {
    if (tagInput.value.trim() && form.tags.length < 3) {
        addTag();
    }
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
                    <label class="block text-sm font-bold text-gray-700 mb-2">해시태그 (필수, 최대 3개)</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span v-for="(t, index) in form.tags" :key="index" class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm font-bold flex items-center shadow-sm">
                            #{{ t }}
                            <button type="button" @click="removeTag(index)" class="ml-1.5 text-indigo-400 hover:text-red-500 rounded-full focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </span>
                    </div>
                    <input 
                        v-if="form.tags.length < 3"
                        v-model="tagInput" 
                        @keydown.enter.prevent="addTag"
                        type="text" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="태그를 입력하고 엔터(Enter)를 누르세요"
                    />
                    <div v-if="form.errors.tags" class="text-red-500 text-sm mt-1">{{ form.errors.tags }}</div>
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

                <!-- Admin Only Section -->
                <div v-if="['admin', 'master'].includes($page.props.auth.user.role)" class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">관리자 전용 설정</p>
                    
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="flex-1">
                            <label for="type" class="block text-sm font-bold text-gray-700 mb-2">게시글 타입</label>
                            <select id="type" v-model="form.type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="general">일반 게시글 (General)</option>
                                <option value="notice">공지사항 (Notice)</option>
                                <option value="ad">광고/홍보 (Ad)</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.is_pinned" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm font-bold text-gray-700">최상단 고정 (Pin to Top)</span>
                            </label>
                        </div>
                    </div>
                </div>

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
