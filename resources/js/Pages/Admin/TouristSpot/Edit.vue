<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    spot: { type: Object, required: true },
});

const form = useForm({
    title:    props.spot.title,
    addr1:    props.spot.addr1 ?? '',
    addr2:    props.spot.addr2 ?? '',
    image:    props.spot.image ?? '',
    thumbnail:props.spot.thumbnail ?? '',
    map_x:    props.spot.map_x ?? '',
    map_y:    props.spot.map_y ?? '',
    tel:      props.spot.tel ?? '',
    overview: props.spot.overview ?? '',
});

const submit = () => form.put(route('admin.tourist-spots.update', props.spot.content_id));
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

            <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
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
                        <label class="block text-sm font-bold text-gray-700 mb-1">대표 이미지 URL</label>
                        <input v-model="form.image" type="url"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        <img v-if="form.image" :src="form.image" class="mt-2 h-20 rounded-lg object-cover" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">썸네일 URL</label>
                        <input v-model="form.thumbnail" type="url"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400" />
                        <img v-if="form.thumbnail" :src="form.thumbnail" class="mt-2 h-20 rounded-lg object-cover" />
                    </div>
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
                    <textarea v-model="form.overview" rows="5"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
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
