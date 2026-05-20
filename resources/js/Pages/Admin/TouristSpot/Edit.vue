<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    spot: { type: Object, required: true },
});

const form = useForm({
    title:                 props.spot.title,
    addr1:                 props.spot.addr1 ?? '',
    addr2:                 props.spot.addr2 ?? '',
    image:                 null,
    thumbnail:             null,
    existing_extra_images: props.spot.extra_images ?? [],
    new_extra_images:      [],
    map_x:                 props.spot.map_x ?? '',
    map_y:                 props.spot.map_y ?? '',
    tel:                   props.spot.tel ?? '',
    overview:              props.spot.overview ?? '',
    usetime:               props.spot.usetime ?? '',
    restdate:              props.spot.restdate ?? '',
    usefee:                props.spot.usefee ?? '',
    parking:               props.spot.parking ?? '',
});

const imagePreview     = ref(props.spot.image || null);
const thumbnailPreview = ref(props.spot.thumbnail || null);
const extraPreviews    = ref([]);

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

// 파일 포함 PUT: POST + _method=put + forceFormData (기존 Post/Edit.vue 패턴과 동일)
const submit = () => form.transform(data => ({ ...data, _method: 'put' }))
    .post(route('admin.tourist-spots.update', props.spot.content_id), { forceFormData: true });
</script>

<template>
    <Head :title="`${spot.title} 수정`" />
    <AdminLayout>
        <div class="max-w-2xl space-y-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tourist-spots.index')" class="text-gray-400 hover:text-gray-600">← 목록</Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">관광지 수정</h1>
                    <p class="text-xs text-gray-400 mt-0.5">
                        출처:
                        <span :class="spot.source === 'manual' ? 'text-orange-600 font-bold' : 'text-blue-600 font-bold'">
                            {{ spot.source === 'manual' ? '직접 추가' : 'API 수집' }}
                        </span>
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- 기본 정보 -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-sm font-black text-gray-500 uppercase tracking-wider">기본 정보</h2>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">관광지명 <span class="text-red-500">*</span></label>
                        <input v-model="form.title" type="text"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">주소</label>
                            <input v-model="form.addr1" type="text"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">상세주소</label>
                            <input v-model="form.addr2" type="text"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">전화번호</label>
                        <input v-model="form.tel" type="text"
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

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">소개글</label>
                        <textarea v-model="form.overview" rows="4"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                    </div>
                </div>

                <!-- 이미지 -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
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

                    <!-- 썸네일 -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">썸네일</label>
                        <img v-if="thumbnailPreview" :src="thumbnailPreview" class="mb-2 h-16 rounded-lg object-cover" />
                        <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                            <input type="file" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-5 file:border-0 file:text-sm file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 transition-all file:rounded-lg cursor-pointer"
                                @input="e => { form.thumbnail = e.target.files[0]; thumbnailPreview = URL.createObjectURL(e.target.files[0]) }" />
                        </div>
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

                <!-- 이용 정보 -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-sm font-black text-gray-500 uppercase tracking-wider">이용 정보 <span class="text-gray-300 font-normal normal-case">(선택)</span></h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">이용시간</label>
                            <textarea v-model="form.usetime" rows="3"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">휴무일</label>
                            <textarea v-model="form.restdate" rows="3"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">이용요금</label>
                            <input v-model="form.usefee" type="text"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">주차</label>
                            <input v-model="form.parking" type="text"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <Link :href="route('admin.tourist-spots.index')"
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">취소</Link>
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-2 bg-orange-500 text-white rounded-lg font-bold text-sm hover:bg-orange-600 disabled:opacity-50 transition-colors">
                        {{ form.processing ? '저장 중...' : '수정 완료' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
