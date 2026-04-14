<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TiptapEditor from '@/Components/TiptapEditor.vue';
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
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-gray-900 border-b-4 border-indigo-600 inline-block pb-1">게시글 관리/중재</h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">부적절한 내용을 수정하거나 카테고리를 조정합니다.</p>
            </div>
            <Link :href="route('admin.posts.index')" class="text-sm font-bold text-gray-600 hover:text-gray-900 px-5 py-2.5 bg-white border border-gray-200 rounded-xl shadow-sm transition-all hover:bg-gray-50">
                &larr; 목록으로 돌아가기
            </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-5xl mx-auto">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Info Lock -->
                <div class="p-4 bg-amber-50 rounded-xl border-l-4 border-amber-500 text-sm text-amber-900 font-bold mb-8 flex items-center gap-3">
                    <span class="text-xl">🛡️</span>
                    <div>
                        <p>관리자 권한으로 본문을 직접 수정 중입니다.</p>
                        <p class="text-[11px] opacity-70">원본 작성자: {{ post.user?.name || '탈퇴회원' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">카테고리 강제 이동</label>
                        <select v-model="form.category_id" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold py-2.5">
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 mb-2">제목 조정</label>
                        <input v-model="form.title" type="text" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold py-2.5" required />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">해시태그 관리 (최대 3개)</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span v-for="(t, index) in form.tags" :key="index" class="bg-indigo-50 text-indigo-700 px-3.5 py-1.5 rounded-full text-xs font-black flex items-center shadow-sm border border-indigo-100">
                            #{{ t }}
                            <button type="button" @click="removeTag(index)" class="ml-2 text-indigo-300 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </span>
                    </div>
                    <input v-if="form.tags.length < 3" v-model="tagInput" @keydown.enter.prevent="addTag" type="text" class="w-full max-w-md rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="태그 입력 후 엔터" />
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 mb-3">본문 내용 수정 (에디터 지원)</label>
                    <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-inner">
                        <TiptapEditor 
                            v-model="form.content" 
                            required
                        />
                    </div>
                </div>

                <div class="flex justify-end pt-8 border-t border-gray-100 mt-6">
                    <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-12 rounded-2xl shadow-xl shadow-indigo-100 transition-all transform active:scale-95 disabled:opacity-50">
                        {{ form.processing ? '저장 중...' : '관리자 권한으로 변경사항 저장' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

