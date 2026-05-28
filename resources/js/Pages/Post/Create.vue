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

const tagInput       = ref('');
const isComposing    = ref(false);
const tagSuggestions = ref([]);
const showSuggestions = ref(false);
let suggestTimer = null;

const addTag = (val = null) => {
    if (!val && isComposing.value) return;
    const v = (val ?? tagInput.value).trim();
    if (v && !form.tags.includes(v) && form.tags.length < 3) {
        form.tags.push(v);
    }
    tagInput.value = '';
    tagSuggestions.value = [];
    showSuggestions.value = false;
};

const removeTag = (index) => {
    form.tags.splice(index, 1);
};

const onTagInput = () => {
    clearTimeout(suggestTimer);
    const q = tagInput.value.trim();
    if (!q) { tagSuggestions.value = []; showSuggestions.value = false; return; }
    suggestTimer = setTimeout(async () => {
        try {
            const res = await fetch(`/api/tags/suggest?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            tagSuggestions.value = data.filter(s => !form.tags.includes(s.tag));
            showSuggestions.value = tagSuggestions.value.length > 0;
        } catch { tagSuggestions.value = []; }
    }, 250);
};

const hideSuggestions = () => {
    setTimeout(() => { showSuggestions.value = false; }, 150);
};

const hasUnsavedContent = computed(() => {
    const hasTitle   = form.title.trim() !== '';
    const hasContent = form.content !== '' && form.content !== '<p></p>';
    const hasTags    = form.tags.length > 0;
    return hasTitle || hasContent || hasTags;
});

// ── 썸네일 드래그&드롭 ─────────────────────────────────────────
const thumbnailInputRef = ref(null);
const thumbnailPreview  = ref(null);
const thumbnailDragOver = ref(false);

const applyThumbnailFile = (file) => {
    if (!file || !file.type.startsWith('image/')) return;
    form.image = file;
    thumbnailPreview.value = URL.createObjectURL(file);
};

const handleThumbnailSelect = (e) => {
    applyThumbnailFile(e.target.files[0]);
};

const handleThumbnailDrop = (e) => {
    thumbnailDragOver.value = false;
    applyThumbnailFile(e.dataTransfer.files[0]);
};

const removeThumbnail = () => {
    form.image = null;
    thumbnailPreview.value = null;
    if (thumbnailInputRef.value) thumbnailInputRef.value.value = '';
};

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
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        해시태그 <span class="text-gray-400 font-medium">(선택, 최대 3개)</span>
                    </label>
                    <!-- 추가된 태그 -->
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span v-for="(t, index) in form.tags" :key="index" class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm font-bold flex items-center shadow-sm">
                            #{{ t }}
                            <button type="button" @click="removeTag(index)" class="ml-1.5 text-indigo-400 hover:text-red-500 rounded-full focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </span>
                    </div>
                    <!-- 입력창 + 자동완성 -->
                    <div v-if="form.tags.length < 3" class="relative">
                        <div class="flex gap-2">
                            <input
                                v-model="tagInput"
                                @input="onTagInput"
                                @keydown.enter.prevent="addTag()"
                                @keydown.escape="showSuggestions = false"
                                @compositionstart="isComposing = true"
                                @compositionend="isComposing = false; onTagInput()"
                                @blur="hideSuggestions"
                                type="text"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="태그 입력 후 엔터 (예: 대구맛집)"
                            />
                            <button
                                type="button"
                                @click="addTag()"
                                class="px-5 py-2 bg-indigo-50 text-indigo-700 rounded-md font-bold hover:bg-indigo-100 transition-colors shadow-sm"
                            >
                                추가
                            </button>
                        </div>
                        <!-- 자동완성 드롭다운 -->
                        <div
                            v-if="showSuggestions"
                            class="absolute z-50 left-0 right-12 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden"
                        >
                            <button
                                v-for="s in tagSuggestions"
                                :key="s.tag"
                                type="button"
                                @mousedown.prevent="addTag(s.tag)"
                                class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-indigo-50 transition-colors text-left"
                            >
                                <span class="text-sm font-bold text-gray-800">#{{ s.tag }}</span>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">태그를 달면 관련 글 추천에 노출됩니다.</p>
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

                <!-- 썸네일 이미지 드래그&드롭 업로드 -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">썸네일 이미지 (목록용, 선택)</label>

                    <!-- 미리보기 상태 -->
                    <div v-if="thumbnailPreview" class="relative group rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                        <img :src="thumbnailPreview" alt="썸네일 미리보기" class="w-full max-h-56 object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100">
                            <button
                                type="button"
                                @click="thumbnailInputRef.click()"
                                class="bg-white text-gray-700 text-xs font-semibold px-3 py-1.5 rounded-lg shadow hover:bg-gray-100 transition"
                            >이미지 변경</button>
                            <button
                                type="button"
                                @click="removeThumbnail"
                                class="bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow hover:bg-red-600 transition"
                            >삭제</button>
                        </div>
                    </div>

                    <!-- 드롭존 (미리보기 없을 때) -->
                    <div
                        v-else
                        @dragover.prevent="thumbnailDragOver = true"
                        @dragleave.prevent="thumbnailDragOver = false"
                        @drop.prevent="handleThumbnailDrop"
                        @click="thumbnailInputRef.click()"
                        :class="[
                            'cursor-pointer border-2 border-dashed rounded-xl p-8 text-center transition-all',
                            thumbnailDragOver
                                ? 'border-indigo-400 bg-indigo-50'
                                : 'border-gray-300 bg-gray-50 hover:border-indigo-300 hover:bg-indigo-50/50'
                        ]"
                    >
                        <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-500">이미지를 드래그하거나 클릭하여 업로드</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP, GIF · 최대 5MB</p>
                    </div>

                    <input
                        ref="thumbnailInputRef"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleThumbnailSelect"
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
