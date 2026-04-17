<script setup>
import { useForm, Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TiptapEditor from '@/Components/TiptapEditor.vue';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    categories: Array
});

const form = useForm({
    category_id: '',
    title: '',
    content: '',
    tags: [],
    image: null,
    is_pinned: false,
});

// 카테고리가 1개면 자동 선택
onMounted(() => {
    if (props.categories.length === 1) {
        form.category_id = props.categories[0].id;
    }
});

const tagInput = ref('');
const isComposing = ref(false);
const addTag = () => {
    if (isComposing.value) return;
    const val = tagInput.value.trim();
    if (val && !form.tags.includes(val) && form.tags.length < 3) {
        form.tags.push(val);
    }
    tagInput.value = '';
};
const removeTag = (index) => {
    form.tags.splice(index, 1);
};

// 내비게이션 보호: 작성 중 이탈 방지
const handleBeforeUnload = (event) => {
    if (form.isDirty) {
        event.preventDefault();
        event.returnValue = '';
    }
};

// 브라우저 뒤로가기 버튼 감지 및 차단
const handlePopState = (event) => {
    if (form.isDirty) {
        // 이미 주소가 바뀌었으므로, 취소 시 현재 주소로 다시 밀어넣어 페이지를 고정함
        if (!confirm('공지사항을 작성 중입니다. 정말 나가시겠습니까?')) {
            history.pushState(null, '', window.location.href);
        }
    }
};

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
    window.addEventListener('popstate', handlePopState);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
    window.removeEventListener('popstate', handlePopState);
});

// Inertia 내부 내비게이션 보호
const removeRouterListener = router.on('before', (event) => {
    if (form.isDirty && !confirm('공지사항을 작성 중입니다. 정말 나가시겠습니까?')) {
        event.preventDefault();
    }
});

onUnmounted(() => {
    removeRouterListener();
});

const submit = () => {
    if (tagInput.value.trim() && form.tags.length < 3) {
        addTag();
    }
    form.post(route('admin.notices.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="공지사항 작성 - Admin" />

    <AdminLayout>
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 border-b-4 border-amber-500 inline-block pb-1">새 공지사항 작성</h1>
                <p class="mt-2 text-sm text-gray-600">서비스 이용자들에게 전달할 중요한 소식을 작성합니다.</p>
            </div>
            <Link :href="route('admin.notices.index')" class="text-gray-500 hover:text-gray-700 font-bold flex items-center gap-1 transition-colors">
                &larr; 목록으로 돌아가기
            </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-bold text-gray-700 mb-2 font-black">공지 카테고리 <span class="text-amber-500 text-xs font-bold ml-1">· 공지 전용만 표시</span></label>

                        <!-- 공지 카테고리가 있을 때 -->
                        <select
                            v-if="categories.length > 0"
                            id="category_id"
                            v-model="form.category_id"
                            class="w-full rounded-xl border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 font-bold"
                            required
                        >
                            <option value="" disabled>공지 분류를 선택하세요</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.slug === 'all' ? '📢 ' : '' }}{{ cat.name }}
                            </option>
                        </select>

                        <!-- 공지 카테고리가 없을 때 경고 -->
                        <div v-else class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-bold">
                            <span>⚠️</span>
                            <span>공지 전용 카테고리(slug: 'all' 또는 'notice')가 없습니다. 카테고리를 먼저 추가해주세요.</span>
                        </div>

                        <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1 font-bold">{{ form.errors.category_id }}</div>
                    </div>

                    <div class="flex items-center pt-6">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.is_pinned" class="rounded-lg border-gray-300 text-amber-500 shadow-sm focus:ring-amber-500 w-5 h-5">
                            <span class="ml-3 text-sm font-bold text-gray-700 group-hover:text-amber-600 transition-colors">홈 피드 최상단 고정 (Important Notice)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-bold text-gray-700 mb-2 font-black">공지 제목</label>
                    <input 
                        id="title" 
                        v-model="form.title" 
                        type="text" 
                        class="w-full rounded-xl border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 font-bold" 
                        placeholder="공지사항 제목을 입력하세요"
                        required
                    />
                    <div v-if="form.errors.title" class="text-red-500 text-sm mt-1 font-bold">{{ form.errors.title }}</div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 font-black">태그 (최대 3개)</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span v-for="(t, index) in form.tags" :key="index" class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-sm font-bold flex items-center border border-amber-100 shadow-sm">
                            #{{ t }}
                            <button type="button" @click="removeTag(index)" class="ml-1.5 text-amber-300 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </span>
                    </div>
                    <div v-if="form.tags.length < 3" class="flex gap-2">
                        <input 
                            v-model="tagInput" 
                            @keydown.enter.prevent="addTag"
                            @compositionstart="isComposing = true"
                            @compositionend="isComposing = false"
                            type="text" 
                            class="flex-1 rounded-xl border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 font-bold" 
                            placeholder="태그 입력 후 엔터 또는 추가"
                        />
                        <button 
                            type="button" 
                            @click="addTag"
                            class="px-5 py-2.5 bg-amber-50 text-amber-700 rounded-xl font-black text-xs hover:bg-amber-100 transition-all border border-amber-100 shadow-sm"
                        >
                            추가
                        </button>
                    </div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-bold text-gray-700 mb-2 font-black">공지 본문</label>
                    <TiptapEditor 
                        id="content" 
                        v-model="form.content" 
                        required
                    />
                    <div v-if="form.errors.content" class="text-red-500 text-sm mt-1 font-bold">{{ form.errors.content }}</div>
                </div>

                <div>
                    <label for="image" class="block text-sm font-bold text-gray-700 mb-2 font-black">공지 썸네일 (필요 시)</label>
                    <div class="flex items-center gap-4 border border-gray-100 p-4 rounded-xl bg-gray-50/50">
                        <input 
                            id="image" 
                            type="file" 
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all file:rounded-xl cursor-pointer" 
                            @input="e => form.image = e.target.files[0]"
                        />
                    </div>
                    <div v-if="form.errors.image" class="text-red-500 text-sm mt-1 font-bold">{{ form.errors.image }}</div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-100 gap-4">
                    <Link 
                        :href="route('admin.notices.index')"
                        class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-all"
                    >
                        취소
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-10 py-2.5 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="form.processing" class="animate-spin text-sm">⏳</span>
                        {{ form.processing ? '등록 중...' : '등록하기' }}
                    </button>

                </div>
            </form>
        </div>
    </AdminLayout>
</template>
