<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
    content_id: '',
    title:      '',
    addr1:      '',
    addr2:      '',
    image:      '',
    thumbnail:  '',
    map_x:      '',
    map_y:      '',
    tel:        '',
    overview:   '',
});

const submit = () => form.post(route('admin.tourist-spots.store'));
</script>

<template>
    <Head title="관광지 직접 추가" />
    <AdminLayout>
        <div class="max-w-2xl space-y-6">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tourist-spots.index')" class="text-gray-400 hover:text-gray-600">← 목록</Link>
                <h1 class="text-2xl font-bold text-gray-900">관광지 직접 추가</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">고유 ID <span class="text-red-500">*</span></label>
                    <input v-model="form.content_id" type="text" placeholder="예: manual_001"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    <p class="text-xs text-gray-400 mt-1">중복되지 않는 고유값 입력 (예: manual_수성못)</p>
                    <p v-if="form.errors.content_id" class="text-xs text-red-500 mt-1">{{ form.errors.content_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">관광지명 <span class="text-red-500">*</span></label>
                    <input v-model="form.title" type="text" placeholder="관광지 이름"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">주소</label>
                        <input v-model="form.addr1" type="text" placeholder="대구광역시 수성구..."
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">상세주소</label>
                        <input v-model="form.addr2" type="text" placeholder="(수성동4가)"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">전화번호</label>
                    <input v-model="form.tel" type="text" placeholder="053-000-0000"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">대표 이미지 URL</label>
                        <input v-model="form.image" type="url" placeholder="https://..."
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">썸네일 URL</label>
                        <input v-model="form.thumbnail" type="url" placeholder="https://..."
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                    </div>
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

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">소개글</label>
                    <textarea v-model="form.overview" rows="5" placeholder="관광지 소개..."
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <Link :href="route('admin.tourist-spots.index')"
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
