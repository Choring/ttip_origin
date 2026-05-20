<script setup>
import { ref } from 'vue';
import { useForm, Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const CATEGORIES = ['한식', '양식', '일식', '중식', '카페', '이색음식점', '기타'];

const props = defineProps({
    restaurant: { type: Object, required: true },
});

const form = useForm({
    title:                 props.restaurant.title,
    category:              props.restaurant.category,
    address:               props.restaurant.address  ?? '',
    image:                 null,
    existing_extra_images: props.restaurant.extra_images ?? [],
    new_extra_images:      [],
    homepage:              props.restaurant.homepage ?? '',
    tel:                   props.restaurant.tel      ?? '',
    map_x:                 props.restaurant.map_x    ?? '',
    map_y:                 props.restaurant.map_y    ?? '',
});

const imagePreview  = ref(props.restaurant.image || null);
const extraPreviews = ref([]);

function onExtraImagesChange(e) {
    Array.from(e.target.files).forEach(file => {
        form.new_extra_images.push(file);
        extraPreviews.value.push(URL.createObjectURL(file));
    });
    e.target.value = '';
}

function removeNewExtra(i) {
    form.new_extra_images.splice(i, 1);
    URL.revokeObjectURL(extraPreviews.value[i]);
    extraPreviews.value.splice(i, 1);
}

function removeExistingExtra(i) {
    form.existing_extra_images.splice(i, 1);
}

const submit = () => form.transform(data => ({ ...data, _method: 'put' }))
    .post(route('admin.restaurants.update', props.restaurant.content_id), { forceFormData: true });

const destroy = () => {
    if (!confirm(`"${props.restaurant.title}" 을(를) 삭제하시겠습니까?`)) return;
    router.delete(route('admin.restaurants.destroy', props.restaurant.content_id));
};
</script>

<template>
    <Head :title="`${restaurant.title} 수정`" />
    <AdminLayout>
        <div class="max-w-2xl space-y-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.restaurants.index')" class="text-gray-400 hover:text-gray-600">← 목록</Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">맛집 수정</h1>
                    <p class="text-xs text-gray-400 mt-0.5">ID: {{ restaurant.content_id }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- 기본 정보 -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-sm font-black text-gray-500 uppercase tracking-wider">기본 정보</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">맛집명 <span class="text-red-500">*</span></label>
                            <input v-model="form.title" type="text"
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
                        <input v-model="form.address" type="text"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">전화번호</label>
                        <input v-model="form.tel" type="text"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">홈페이지</label>
                        <input v-model="form.homepage" type="text"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">경도 (mapX)</label>
                            <input v-model="form.map_x" type="text"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">위도 (mapY)</label>
                            <input v-model="form.map_y" type="text"
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
                        <img v-if="imagePreview" :src="imagePreview" class="mb-2 h-24 rounded-lg object-cover" />
                        <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                            <input type="file" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-5 file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all file:rounded-lg cursor-pointer"
                                @input="e => { form.image = e.target.files[0]; imagePreview = URL.createObjectURL(e.target.files[0]) }" />
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">새 파일 선택 시 기존 이미지가 교체됩니다</p>
                        <p v-if="form.errors.image" class="text-xs text-red-500 mt-1">{{ form.errors.image }}</p>
                    </div>

                    <!-- 추가 이미지 -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">추가 이미지</label>

                        <!-- 기존 이미지 -->
                        <div v-if="form.existing_extra_images.length > 0" class="mb-3">
                            <p class="text-[10px] text-gray-400 mb-1.5">현재 이미지 (× 클릭 시 삭제)</p>
                            <div class="grid grid-cols-5 gap-2">
                                <div v-for="(url, i) in form.existing_extra_images" :key="url" class="relative group aspect-square">
                                    <img :src="url" class="w-full h-full object-cover rounded-lg" />
                                    <button type="button" @click="removeExistingExtra(i)"
                                        class="absolute top-0.5 right-0.5 w-5 h-5 bg-black/60 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                                </div>
                            </div>
                        </div>

                        <!-- 새로 추가할 이미지 미리보기 -->
                        <div v-if="extraPreviews.length > 0" class="mb-3">
                            <p class="text-[10px] text-orange-500 mb-1.5">추가될 이미지</p>
                            <div class="grid grid-cols-5 gap-2">
                                <div v-for="(src, i) in extraPreviews" :key="i" class="relative group aspect-square">
                                    <img :src="src" class="w-full h-full object-cover rounded-lg ring-2 ring-orange-400" />
                                    <button type="button" @click="removeNewExtra(i)"
                                        class="absolute top-0.5 right-0.5 w-5 h-5 bg-black/60 text-white rounded-full text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                            <input type="file" accept="image/*" multiple
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-5 file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all file:rounded-lg cursor-pointer"
                                @input="onExtraImagesChange" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button" @click="destroy"
                        class="px-4 py-2 bg-red-50 text-red-600 rounded-lg font-bold hover:bg-red-100 transition-colors text-sm">
                        삭제
                    </button>
                    <div class="flex gap-3">
                        <Link :href="route('admin.restaurants.index')"
                            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">취소</Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-6 py-2 bg-orange-500 text-white rounded-lg font-bold text-sm hover:bg-orange-600 disabled:opacity-50 transition-colors">
                            {{ form.processing ? '저장 중...' : '수정 완료' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
