<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const CATEGORIES = ['한식', '양식', '일식', '중식', '카페', '이색음식점', '기타'];

const form = useForm({
    content_id:       '',
    title:            '',
    category:         '한식',
    address:          '',
    image:            null,
    new_extra_images: [],
    homepage:         '',
    tel:              '',
    map_x:            '',
    map_y:            '',
});

const imagePreview  = ref(null);
const extraPreviews = ref([]);

function onExtraImagesChange(e) {
    Array.from(e.target.files).forEach(file => {
        form.new_extra_images.push(file);
        extraPreviews.value.push(URL.createObjectURL(file));
    });
    e.target.value = '';
}

function removeExtra(i) {
    form.new_extra_images.splice(i, 1);
    URL.revokeObjectURL(extraPreviews.value[i]);
    extraPreviews.value.splice(i, 1);
}

const submit = () => form.post(route('admin.restaurants.store'), { forceFormData: true });
</script>

<template>
    <Head title="맛집 직접 추가" />
    <AdminLayout>
        <div class="max-w-2xl space-y-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.restaurants.index')" class="text-gray-400 hover:text-gray-600">← 목록</Link>
                <h1 class="text-2xl font-bold text-gray-900">맛집 직접 추가</h1>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- 기본 정보 -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-sm font-black text-gray-500 uppercase tracking-wider">기본 정보</h2>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">고유 ID <span class="text-red-500">*</span></label>
                        <input v-model="form.content_id" type="text" placeholder="예: manual_참깨국수"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        <p class="text-xs text-gray-400 mt-1">중복되지 않는 고유값 입력</p>
                        <p v-if="form.errors.content_id" class="text-xs text-red-500 mt-1">{{ form.errors.content_id }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">맛집명 <span class="text-red-500">*</span></label>
                            <input v-model="form.title" type="text" placeholder="식당 이름"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                            <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">카테고리 <span class="text-red-500">*</span></label>
                            <select v-model="form.category"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <option v-for="cat in CATEGORIES" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">주소</label>
                        <input v-model="form.address" type="text" placeholder="대구광역시 수성구..."
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">전화번호</label>
                        <input v-model="form.tel" type="text" placeholder="053-000-0000"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">홈페이지</label>
                        <input v-model="form.homepage" type="text" placeholder="https://..."
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">경도 (mapX)</label>
                            <input v-model="form.map_x" type="text" placeholder="128.620"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">위도 (mapY)</label>
                            <input v-model="form.map_y" type="text" placeholder="35.853"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        </div>
                    </div>
                </div>

                <!-- 이미지 -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-sm font-black text-gray-500 uppercase tracking-wider">이미지</h2>

                    <!-- 대표 이미지 -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">대표 이미지</label>
                        <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                            <input type="file" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-5 file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all file:rounded-lg cursor-pointer"
                                @input="e => { form.image = e.target.files[0]; imagePreview = URL.createObjectURL(e.target.files[0]) }" />
                        </div>
                        <img v-if="imagePreview" :src="imagePreview" class="mt-2 h-24 rounded-lg object-cover" />
                        <p v-if="form.errors.image" class="text-xs text-red-500 mt-1">{{ form.errors.image }}</p>
                    </div>

                    <!-- 추가 이미지 -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            추가 이미지 <span class="text-gray-400 font-normal text-xs">(여러 장 선택 가능)</span>
                        </label>
                        <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                            <input type="file" accept="image/*" multiple
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-5 file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all file:rounded-lg cursor-pointer"
                                @input="onExtraImagesChange" />
                        </div>
                        <div v-if="extraPreviews.length > 0" class="mt-2 grid grid-cols-5 gap-2">
                            <div v-for="(src, i) in extraPreviews" :key="i" class="relative group aspect-square">
                                <img :src="src" class="w-full h-full object-cover rounded-lg" />
                                <button type="button" @click="removeExtra(i)"
                                    class="absolute top-0.5 right-0.5 w-5 h-5 bg-black/60 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <Link :href="route('admin.restaurants.index')"
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">취소</Link>
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-2 bg-orange-500 text-white rounded-lg font-bold text-sm hover:bg-orange-600 disabled:opacity-50 transition-colors">
                        {{ form.processing ? '저장 중...' : '등록' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
