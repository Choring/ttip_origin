<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    post: Object,
    categories: Array
});

const form = useForm({
    category_id: props.post.category_id || '',
    title: props.post.title || '',
    content: props.post.content || '',
    tags: props.post.tags || [],
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
    form.put(route('admin.posts.update', props.post.id));
};
</script>

<template>
    <Head title="게시글 중재 및 수정" />

    <AdminLayout>
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold border-b-4 border-indigo-600 inline-block pb-1">관리자 게시글 관리/수정</h2>
                <p class="mt-2 text-sm text-gray-500">규정 위반 게시물을 쾌적하게 중재하고 편집합니다.</p>
            </div>
            <Link :href="route('admin.posts.index')" class="text-sm font-bold text-gray-600 hover:text-gray-900 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">
                &larr; 목록으로 돌아가기
            </Link>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 max-w-4xl opacity-95">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Info Lock -->
                <div class="p-4 bg-red-50 rounded-lg border-l-4 border-red-500 text-sm text-red-800 font-bold mb-6">
                    🚨 당신은 관리자 권한으로 이 글을 임의 수정하고 있습니다. 원본 작성자: {{ post.user?.name || '탈퇴회원' }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-extrabold text-gray-700 mb-2">게시글 카테고리 강제 이동</label>
                        <select v-model="form.category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-gray-700 mb-2">제목 조작</label>
                    <input v-model="form.title" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                    <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-gray-700 mb-2">해시태그 조정 (최대 3개)</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span v-for="(t, index) in form.tags" :key="index" class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-bold flex items-center shadow-sm border border-gray-200">
                            #{{ t }}
                            <button type="button" @click="removeTag(index)" class="ml-1.5 text-gray-400 hover:text-red-500 rounded-full focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </span>
                    </div>
                    <input v-if="form.tags.length < 3" v-model="tagInput" @keydown.enter.prevent="addTag" type="text" class="w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="태그를 대리 추가하거나 삭제합니다 (Enter)" />
                    <div v-if="form.errors.tags" class="text-red-500 text-sm mt-1">{{ form.errors.tags }}</div>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-gray-700 mb-2">본문 내용 조작 (Markdown 지원)</label>
                    <textarea v-model="form.content" rows="15" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm leading-relaxed bg-gray-50" placeholder="자유롭게 편집 가능합니다." required></textarea>
                    <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                </div>

                <div class="flex justify-end pt-5 border-t border-gray-200 mt-4">
                    <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 px-8 rounded-lg shadow-md transition-all disabled:opacity-50">
                        {{ form.processing ? '저장 중...' : '관리자 권한으로 강제 저장' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
