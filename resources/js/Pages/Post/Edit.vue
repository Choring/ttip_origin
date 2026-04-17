<script setup>
import { useForm, Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import TiptapEditor from '@/Components/TiptapEditor.vue';

const props = defineProps({
    post: Object,
    categories: Array
});

import { ref, computed, onMounted, onUnmounted } from 'vue';

const form = useForm({
    category_id: props.post.category_id || '',
    title: props.post.title || '',
    content: props.post.content || '',
    tags: props.post.tags || [],
    type: props.post.type || 'general',
    is_pinned: props.post.is_pinned || false,
    extra_info: props.post.extra_info || {}, // 기존 정형 데이터 로드
    image: null, // 새 썸네일 이미지
});

// 카테고리별 필드 정의 (Create.vue와 동일)
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

// 내비게이션 보호: 수정 중 이탈 방지
const handleBeforeUnload = (event) => {
    if (form.isDirty) {
        event.preventDefault();
        event.returnValue = '';
    }
};

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
});

// Inertia 내부 내비게이션 보호
const removeRouterListener = router.on('before', (event) => {
    if (form.isDirty && !confirm('수정 중인 내용이 있습니다. 정말 나가시겠습니까?')) {
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
    // 파일을 포함한 업데이트는 POST 요청에 _method: 'put'을 데이터 필드로 담아서 보내야 합니다 (Laravel/Inertia 표준)
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('posts.update', props.post.id), {
        forceFormData: true,
    });
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

                <!-- Dynamic Category Info Card Fields -->
                <div v-if="currentFields.length > 0" class="bg-indigo-50/50 p-6 rounded-xl border border-indigo-100/50 space-y-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                        <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-wider">주요 정보 수정</h3>
                        <span class="text-xs text-indigo-500 font-medium">(정형 데이터)</span>
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
                    <label for="image" class="block text-sm font-bold text-gray-700 mb-2">썸네일 이미지 수정 (선택)</label>
                    <div v-if="post.card_image_url" class="mb-3">
                        <p class="text-xs text-gray-400 mb-1">현재 썸네일:</p>
                        <img :src="post.card_image_url" class="w-32 h-20 object-cover rounded-lg border border-gray-200" alt="Current thumbnail" />
                    </div>
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
