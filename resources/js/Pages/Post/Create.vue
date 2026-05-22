<script setup>
import { useForm, Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import TiptapEditor from '@/Components/TiptapEditor.vue';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    categories: Array
});

const form = useForm({
    category_id: '',
    title: '',
    content: '',
    tags: [],
    image: null,
    type: 'general',
    is_pinned: false,
    extra_info: {},
});

// 카테고리별 필드 정의
const categoryFields = {
    'restaurant': [
        { key: 'location', label: '위치', placeholder: '예: 수성구 범어동' },
        { key: 'price', label: '가격대', placeholder: '예: 1~2만원대' },
        { key: 'waiting', label: '웨이팅', placeholder: '예: 보통 / 있음' },
        { key: 'parking', label: '주차', placeholder: '예: 가능 / 불가' },
    ],
    'cafe': [
        { key: 'location', label: '위치', placeholder: '예: 중구 동성로' },
        { key: 'price', label: '가격대', placeholder: '예: 5천원~' },
        { key: 'outlets', label: '콘센트', placeholder: '예: 많음 / 적음' },
        { key: 'wifi', label: '와이파이', placeholder: '예: 빠름 / 보통' },
    ],
    'solo-dining': [
        { key: 'location', label: '위치', placeholder: '예: 남구 대명동' },
        { key: 'price', label: '가격대', placeholder: '예: 1만원 이하' },
        { key: 'solo_seats', label: '1인석 여부', placeholder: '예: 있음 (바 테이블)' },
        { key: 'waiting', label: '웨이팅', placeholder: '예: 거의 없음' },
    ],
    'gym': [
        { key: 'location', label: '위치', placeholder: '예: 달서구 상인동' },
        { key: 'fee', label: '월회비', placeholder: '예: 3개월 15만원' },
        { key: 'hours', label: '운영시간', placeholder: '예: 06:00 - 24:00' },
        { key: 'facilities', label: '시설', placeholder: '예: 샤워실 완비, 운동복 무료' },
    ],
    'part-time': [
        { key: 'location', label: '위치', placeholder: '예: 북구 침산동' },
        { key: 'wage', label: '시급', placeholder: '예: 10,030원' },
        { key: 'hours', label: '근무시간', placeholder: '예: 주말 오전 09-15' },
        { key: 'industry', label: '업종', placeholder: '예: 편의점 / 카페' },
    ]
};

const currentCategorySlug = computed(() => {
    const cat = props.categories.find(c => c.id === form.category_id);
    return cat ? cat.slug : null;
});

const currentFields = computed(() => {
    return categoryFields[currentCategorySlug.value] || [];
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

const hasUnsavedContent = computed(() => {
    const hasTitle   = form.title.trim() !== '';
    const hasContent = form.content !== '' && form.content !== '<p></p>';
    const hasTags    = form.tags.length > 0;
    return hasTitle || hasContent || hasTags;
});

// ── 임시저장 (localStorage) ──────────────────────────────────────
const DRAFT_KEY    = 'draft_post_create';
const draftSavedAt = ref(null);   // Date 객체
const hasDraft     = ref(false);  // 복원 배너 표시 여부

// 저장된 시각을 "N분 전" 형식으로 변환
const draftSavedLabel = computed(() => {
    if (!draftSavedAt.value) return '';
    const diff = Math.floor((Date.now() - draftSavedAt.value.getTime()) / 1000);
    if (diff < 60)  return '방금 자동저장됨';
    if (diff < 3600) return `${Math.floor(diff / 60)}분 전 자동저장됨`;
    return `${Math.floor(diff / 3600)}시간 전 자동저장됨`;
});

// 임시저장 레이블 갱신용 타이머 (1분마다 갱신)
let labelTimer = null;
const labelTick = ref(0); // computed 재계산을 강제하기 위한 트리거

// debounce 핸들러
let saveTimer = null;
const scheduleSave = () => {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
        if (!hasUnsavedContent.value) return;
        const data = {
            category_id: form.category_id,
            title:       form.title,
            content:     form.content,
            tags:        [...form.tags],
            extra_info:  { ...form.extra_info },
            savedAt:     new Date().toISOString(),
        };
        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
            draftSavedAt.value = new Date();
        } catch (e) {
            // 저장 공간 부족 등 무시
        }
    }, 2000);
};

// form 필드 변경 감지 → 임시저장 예약
watch(
    () => ({
        category_id: form.category_id,
        title:       form.title,
        content:     form.content,
        tags:        [...form.tags],
        extra_info:  { ...form.extra_info },
    }),
    scheduleSave,
    { deep: true }
);

const clearDraft = () => {
    clearTimeout(saveTimer);
    localStorage.removeItem(DRAFT_KEY);
    draftSavedAt.value = null;
    hasDraft.value     = false;
};

const restoreDraft = () => {
    try {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (!raw) return;
        const data = JSON.parse(raw);
        form.category_id = data.category_id || '';
        form.title       = data.title       || '';
        form.content     = data.content     || '';
        form.tags        = data.tags        || [];
        form.extra_info  = data.extra_info  || {};
        draftSavedAt.value = data.savedAt ? new Date(data.savedAt) : null;
    } catch {
        clearDraft();
    } finally {
        hasDraft.value = false;
    }
};

const discardDraft = () => {
    clearDraft();
};

onMounted(() => {
    // 임시저장 확인
    try {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (raw) {
            const data = JSON.parse(raw);
            if (data.title?.trim() || (data.content && data.content !== '<p></p>')) {
                hasDraft.value     = true;
                draftSavedAt.value = data.savedAt ? new Date(data.savedAt) : null;
            }
        }
    } catch {
        clearDraft();
    }

    // 레이블 갱신 타이머
    labelTimer = setInterval(() => { labelTick.value++; }, 60_000);
});

onUnmounted(() => {
    clearTimeout(saveTimer);
    clearInterval(labelTimer);
});

// 취소 버튼
const handleCancel = () => {
    if (hasUnsavedContent.value) {
        if (!confirm('작성 중인 내용이 있습니다. 정말 나가시겠습니까?\n(임시저장 내용은 유지됩니다)')) {
            return;
        }
    }
    router.visit(route('home'));
};

const submit = () => {
    if (tagInput.value.trim() && form.tags.length < 3) {
        addTag();
    }
    form.post(route('posts.store'), {
        forceFormData: true,
        onSuccess: () => {
            clearDraft();
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="새 글 쓰기" />

    <MainLayout>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold mb-6">새 글 쓰기</h2>

            <!-- 임시저장 복원 배너 -->
            <Transition name="draft-banner">
                <div
                    v-if="hasDraft"
                    class="mb-6 flex items-center justify-between gap-4 p-4 bg-indigo-50 border border-indigo-200 rounded-xl"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-indigo-800">임시저장된 글이 있습니다</p>
                            <p class="text-xs text-indigo-500 mt-0.5">
                                {{ draftSavedAt ? new Date(draftSavedAt).toLocaleString('ko-KR', { month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) + ' 저장됨' : '이전에 작성하던 내용이 있습니다' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button
                            type="button"
                            @click="restoreDraft"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-colors"
                        >
                            이어서 작성
                        </button>
                        <button
                            type="button"
                            @click="discardDraft"
                            class="px-4 py-2 bg-white text-gray-500 text-sm font-bold rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                        >
                            삭제
                        </button>
                    </div>
                </div>
            </Transition>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="category_id" class="block text-sm font-bold text-gray-700 mb-2">카테고리</label>
                    <select
                        id="category_id"
                        v-model="form.category_id"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                        <option value="" disabled selected>게시글 분류를 선택하세요</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
                </div>

                <!-- Dynamic Category Info Card Fields -->
                <div v-if="currentFields.length > 0" class="bg-indigo-50/50 p-6 rounded-xl border border-indigo-100/50 space-y-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                        <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider">주요 정보 입력</h3>
                        <span class="text-xs text-indigo-500 font-medium">(정형 데이터로 보관됩니다)</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="field in currentFields" :key="field.key">
                            <label :for="field.key" class="block text-xs font-bold text-indigo-700 mb-1.5 ml-1">{{ field.label }}</label>
                            <input
                                :id="field.key"
                                v-model="form.extra_info[field.key]"
                                type="text"
                                class="w-full rounded-lg border-indigo-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white placeholder-gray-300 text-sm"
                                :placeholder="field.placeholder"
                            />
                        </div>
                    </div>
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
                    <div v-if="form.tags.length < 3" class="flex gap-2">
                        <input
                            v-model="tagInput"
                            @keydown.enter.prevent="addTag"
                            @compositionstart="isComposing = true"
                            @compositionend="isComposing = false"
                            type="text"
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="태그 입력 후 엔터 또는 추가 버튼 클릭"
                        />
                        <button
                            type="button"
                            @click="addTag"
                            class="px-5 py-2 bg-indigo-50 text-indigo-700 rounded-md font-bold hover:bg-indigo-100 transition-colors shadow-sm"
                        >
                            추가
                        </button>
                    </div>
                    <div v-if="form.errors.tags" class="text-red-500 text-sm mt-1">{{ form.errors.tags }}</div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-bold text-gray-700 mb-2">본문</label>
                    <TiptapEditor
                        id="content"
                        v-model="form.content"
                        required
                    />
                    <div v-if="form.errors.content" class="text-red-500 text-sm mt-1">{{ form.errors.content }}</div>
                </div>

                <div>
                    <label for="image" class="block text-sm font-bold text-gray-700 mb-2">썸네일 이미지 (목록용, 선택)</label>
                    <input
                        id="image"
                        type="file"
                        accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 focus:outline-none"
                        @input="e => form.image = e.target.files[0]"
                    />
                    <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
                </div>

                <!-- Admin Only Section -->
                <div v-if="['admin', 'master'].includes($page.props.auth.user.role)" class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">관리자 전용 설정</p>

                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="flex-1">
                            <label for="type" class="block text-sm font-bold text-gray-700 mb-2">게시글 타입</label>
                            <select id="type" v-model="form.type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="general">일반 게시글 (General)</option>
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

                <!-- 하단 버튼 + 임시저장 상태 표시 -->
                <div class="flex justify-between items-center pt-4">
                    <!-- 임시저장 상태 표시 -->
                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <template v-if="draftSavedAt && !hasDraft">
                            <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ draftSavedLabel }}</span>
                            <button
                                type="button"
                                @click="clearDraft"
                                class="ml-1 text-gray-300 hover:text-red-400 transition-colors"
                                title="임시저장 삭제"
                            >
                                ✕
                            </button>
                        </template>
                    </div>

                    <!-- 버튼 그룹 -->
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="handleCancel"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        >
                            취소
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? '작성 중...' : '작성하기' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<style scoped>
.draft-banner-enter-active,
.draft-banner-leave-active {
    transition: all 0.3s ease;
}
.draft-banner-enter-from,
.draft-banner-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
