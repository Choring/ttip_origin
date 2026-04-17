<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TiptapEditor from '@/Components/TiptapEditor.vue';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    post: Object,
    categories: Array
});

const POST_TYPES = [
    { value: 'general',  label: '일반 게시글',   icon: '📝', desc: '기본 게시글. 카테고리 피드에 노출됩니다.' },
    { value: 'notice',   label: '공지사항',       icon: '📢', desc: '공지사항 피드에 노출됩니다.' },
    { value: 'pinned',   label: '상단 고정 공지', icon: '📌', desc: '홈 피드 최상단에 항상 고정 노출됩니다. (최대 3개)' },
];

const form = useForm({
    category_id: props.post.category_id || '',
    title:       props.post.title       || '',
    content:     props.post.content     || '',
    tags:        props.post.tags        || [],
    type:        props.post.type        || 'general',
    is_pinned:   props.post.is_pinned   ?? false,
});

// type이 pinned이면 is_pinned 자동 활성화
const onTypeChange = () => {
    if (form.type === 'pinned') {
        form.is_pinned = true;
    } else if (form.type !== 'pinned') {
        form.is_pinned = false;
    }
};

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

// 내비게이션 보호: 수정 중 이탈 방지
const handleBeforeUnload = (event) => {
    if (form.isDirty) {
        event.preventDefault();
        event.returnValue = '';
    }
};

// 브라우저 뒤로가기 버튼 감지 및 차단
const handlePopState = (event) => {
    if (form.isDirty) {
        if (!confirm('공지 설정을 수정 중입니다. 정말 나가시겠습니까?')) {
            // 현재 페이지 주소를 다시 히스토리에 밀어넣어 이동을 취소한 것처럼 처리
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
    if (form.isDirty && !confirm('공지 설정을 수정 중입니다. 정말 나가시겠습니까?')) {
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
    form.put(route('admin.posts.update', props.post.id));
};
</script>

<template>
    <Head title="게시글 중재 및 수정" />

    <AdminLayout>
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-gray-900 border-b-4 border-indigo-600 inline-block pb-1">게시글 관리/중재</h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">부적절한 내용을 수정하거나 카테고리·공지 타입을 조정합니다.</p>
            </div>
            <Link :href="route('admin.posts.index')" class="text-sm font-bold text-gray-600 hover:text-gray-900 px-5 py-2.5 bg-white border border-gray-200 rounded-xl shadow-sm transition-all hover:bg-gray-50">
                &larr; 목록으로 돌아가기
            </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-5xl mx-auto">
            <form @submit.prevent="submit" class="space-y-8">

                <!-- 원작자 정보 뱃지 -->
                <div class="p-4 bg-amber-50 rounded-xl border-l-4 border-amber-500 text-sm text-amber-900 font-bold flex items-center gap-3">
                    <span class="text-xl">🛡️</span>
                    <div>
                        <p>관리자 권한으로 본문을 직접 수정 중입니다.</p>
                        <p class="text-[11px] opacity-70">원본 작성자: {{ post.user?.name || '탈퇴회원' }} · 게시글 ID: #{{ post.id }}</p>
                    </div>
                </div>

                <!-- ─── 섹션 1: 기본 정보 ─── -->
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">기본 정보</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-2">카테고리 강제 이동</label>
                            <select v-model="form.category_id" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold py-2.5">
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.category_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-2">제목 조정</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold py-2.5"
                                required
                            />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.title }}</p>
                        </div>
                    </div>
                </div>

                <!-- ─── 섹션 2: 공지 타입 설정 ─── -->
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">공지 타입 설정 <span class="normal-case text-indigo-400">· 관리자 전용</span></h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label
                            v-for="t in POST_TYPES"
                            :key="t.value"
                            class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                            :class="form.type === t.value
                                ? 'border-indigo-500 bg-indigo-50 shadow-md shadow-indigo-100'
                                : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'"
                        >
                            <input
                                type="radio"
                                :value="t.value"
                                v-model="form.type"
                                @change="onTypeChange"
                                class="sr-only"
                            />
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xl">{{ t.icon }}</span>
                                <span class="text-sm font-black" :class="form.type === t.value ? 'text-indigo-700' : 'text-gray-700'">
                                    {{ t.label }}
                                </span>
                                <span v-if="form.type === t.value" class="ml-auto w-4 h-4 bg-indigo-500 rounded-full flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 12 12">
                                        <path d="M10 3L5 8.5 2 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                                    </svg>
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-400 font-medium leading-tight">{{ t.desc }}</p>
                        </label>
                    </div>

                    <!-- 상단 고정 토글 (pinned가 아닐 때만 별도 체크박스) -->
                    <div v-if="form.type !== 'pinned'" class="mt-3 flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <input
                            id="is_pinned"
                            type="checkbox"
                            v-model="form.is_pinned"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        />
                        <label for="is_pinned" class="text-sm font-bold text-gray-700 cursor-pointer">
                            📌 홈 피드 상단 고정 (is_pinned)
                        </label>
                        <span class="text-xs text-gray-400 font-medium">· 타입과 무관하게 항상 상단 노출</span>
                    </div>
                </div>

                <!-- ─── 섹션 3: 해시태그 ─── -->
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">해시태그 관리</h3>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span
                            v-for="(t, index) in form.tags"
                            :key="index"
                            class="bg-indigo-50 text-indigo-700 px-3.5 py-1.5 rounded-full text-xs font-black flex items-center shadow-sm border border-indigo-100"
                        >
                            #{{ t }}
                            <button type="button" @click="removeTag(index)" class="ml-2 text-indigo-300 hover:text-red-500 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                        <span v-if="form.tags.length === 0" class="text-xs text-gray-400 font-medium italic py-1.5">등록된 태그 없음</span>
                    </div>
                    <div v-if="form.tags.length < 3" class="flex gap-2 max-w-md">
                        <input
                            v-model="tagInput"
                            @keydown.enter.prevent="addTag"
                            @compositionstart="isComposing = true"
                            @compositionend="isComposing = false"
                            type="text"
                            class="flex-1 rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5"
                            placeholder="태그 입력 후 엔터 또는 추가"
                        />
                        <button 
                            type="button" 
                            @click="addTag"
                            class="px-5 py-2.5 bg-indigo-50 text-indigo-700 rounded-xl font-black text-xs hover:bg-indigo-100 transition-all border border-indigo-100 shadow-sm"
                        >
                            추가
                        </button>
                    </div>
                </div>

                <!-- ─── 섹션 4: 본문 에디터 ─── -->
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">본문 내용 수정 <span class="normal-case text-indigo-400">· TipTap 에디터</span></h3>
                    <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-inner">
                        <TiptapEditor
                            v-model="form.content"
                            required
                        />
                    </div>
                    <p v-if="form.errors.content" class="mt-1 text-xs text-red-500 font-semibold">{{ form.errors.content }}</p>
                </div>

                <!-- 제출 버튼 -->
                <div class="flex items-center justify-between pt-8 border-t border-gray-100 mt-6">
                    <!-- 현재 타입 요약 -->
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <span>현재 설정:</span>
                        <span class="font-black text-indigo-600">
                            {{ POST_TYPES.find(t => t.value === form.type)?.icon }}
                            {{ POST_TYPES.find(t => t.value === form.type)?.label }}
                        </span>
                        <span v-if="form.is_pinned" class="text-amber-600 font-bold">+ 📌 상단고정</span>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-12 rounded-2xl shadow-xl shadow-indigo-100 transition-all transform active:scale-95 disabled:opacity-50 flex items-center gap-2"
                    >
                        <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ form.processing ? '저장 중...' : '관리자 권한으로 변경사항 저장' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
