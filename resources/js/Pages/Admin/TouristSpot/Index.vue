<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const debounce = (fn, delay = 300) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const props = defineProps({
    spots:   { type: Object, default: () => ({ data: [], links: [], total: 0 }) },
    filters: { type: Object, default: () => ({ search: '', source: '' }) },
});

const search = ref(props.filters?.search || '');
const source = ref(props.filters?.source || '');

const applyFilters = () => {
    router.get(route('admin.tourist-spots.index'), {
        search: search.value,
        source: source.value,
    }, { preserveState: true, replace: true });
};

watch(search, debounce(applyFilters, 300));
watch(source, applyFilters);

const destroy = (contentId, title) => {
    if (!confirm(`"${title}" 을(를) 삭제하시겠습니까?`)) return;
    router.delete(route('admin.tourist-spots.destroy', contentId), { preserveScroll: true });
};
</script>

<template>
    <Head title="관광지 관리" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">관광지 관리</h1>
                <Link :href="route('admin.tourist-spots.create')"
                    class="px-4 py-2 bg-orange-500 text-white rounded-lg font-bold hover:bg-orange-600 transition-colors text-sm">
                    + 직접 추가
                </Link>
            </div>

            <!-- 필터 -->
            <div class="flex gap-3">
                <input
                    v-model="search"
                    type="text"
                    placeholder="관광지 이름 검색..."
                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-orange-400"
                />
                <select v-model="source"
                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">전체</option>
                    <option value="api">API 수집</option>
                    <option value="manual">직접 추가</option>
                </select>
            </div>

            <!-- 테이블 -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">관광지명</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">주소</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">출처</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">수집일시</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">관리</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="spot in spots.data" :key="spot.content_id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <div class="flex items-center gap-2">
                                    <img v-if="spot.thumbnail" :src="spot.thumbnail" class="w-8 h-8 rounded-lg object-cover" />
                                    <div v-else class="w-8 h-8 rounded-lg bg-gray-200"></div>
                                    {{ spot.title }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ spot.addr1 }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                    :class="spot.source === 'manual'
                                        ? 'bg-orange-100 text-orange-700'
                                        : 'bg-blue-100 text-blue-700'">
                                    {{ spot.source === 'manual' ? '직접 추가' : 'API' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ spot.fetched_at ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <Link :href="route('admin.tourist-spots.edit', spot.content_id)"
                                        class="text-xs text-blue-600 hover:underline font-bold">수정</Link>
                                    <button @click="destroy(spot.content_id, spot.title)"
                                        class="text-xs text-red-500 hover:underline font-bold">삭제</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="spots.data.length === 0">
                            <td colspan="5" class="text-center py-12 text-gray-400">데이터가 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="flex gap-1 justify-center">
                <Link v-for="link in spots.links" :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-3 py-1.5 rounded text-sm border"
                    :class="link.active
                        ? 'bg-orange-500 text-white border-orange-500 font-bold'
                        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                />
            </div>
        </div>
    </AdminLayout>
</template>
