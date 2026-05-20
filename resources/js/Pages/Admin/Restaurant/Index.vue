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
    restaurants: { type: Object, default: () => ({ data: [], links: [], total: 0 }) },
    filters:     { type: Object, default: () => ({ search: '', category: '' }) },
});

const search   = ref(props.filters?.search   || '');
const category = ref(props.filters?.category || '');

const CATEGORIES = ['한식', '양식', '일식', '중식', '카페', '이색음식점', '기타'];

const applyFilters = () => {
    router.get(route('admin.restaurants.index'), {
        search:   search.value,
        category: category.value,
    }, { preserveState: true, replace: true });
};

watch(search, debounce(applyFilters, 300));
watch(category, applyFilters);

const destroy = (contentId, title) => {
    if (!confirm(`"${title}" 을(를) 삭제하시겠습니까?`)) return;
    router.delete(route('admin.restaurants.destroy', contentId), { preserveScroll: true });
};

const categoryStyle = (cat) => {
    const map = {
        '한식':      'bg-red-100 text-red-700',
        '양식':      'bg-yellow-100 text-yellow-700',
        '일식':      'bg-pink-100 text-pink-700',
        '중식':      'bg-orange-100 text-orange-700',
        '카페':      'bg-amber-100 text-amber-700',
        '이색음식점': 'bg-purple-100 text-purple-700',
        '기타':      'bg-gray-100 text-gray-600',
    };
    return map[cat] || 'bg-gray-100 text-gray-600';
};
</script>

<template>
    <Head title="맛집 관리" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">맛집 관리</h1>
                    <p class="text-sm text-gray-500 mt-1">총 {{ restaurants.total }}개</p>
                </div>
                <Link :href="route('admin.restaurants.create')"
                    class="px-4 py-2 bg-orange-500 text-white rounded-lg font-bold hover:bg-orange-600 transition-colors text-sm">
                    + 직접 추가
                </Link>
            </div>

            <!-- 필터 -->
            <div class="flex gap-3 flex-wrap">
                <input
                    v-model="search"
                    type="text"
                    placeholder="이름·주소 검색..."
                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-orange-400"
                />
                <select v-model="category"
                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">전체 카테고리</option>
                    <option v-for="cat in CATEGORIES" :key="cat" :value="cat">{{ cat }}</option>
                </select>
            </div>

            <!-- 테이블 -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">맛집명</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">카테고리</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">주소</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">전화번호</th>
                            <th class="text-left px-4 py-3 font-bold text-gray-600">관리</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="r in restaurants.data" :key="r.content_id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <div class="flex items-center gap-2">
                                    <img v-if="r.image" :src="r.image" class="w-8 h-8 rounded-lg object-cover flex-shrink-0" />
                                    <div v-else class="w-8 h-8 rounded-lg bg-gray-200 flex-shrink-0"></div>
                                    <span class="line-clamp-1">{{ r.title }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold" :class="categoryStyle(r.category)">
                                    {{ r.category }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs">
                                <span class="line-clamp-1">{{ r.address || '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ r.tel || '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <Link :href="route('admin.restaurants.edit', r.content_id)"
                                        class="text-xs text-blue-600 hover:underline font-bold">수정</Link>
                                    <button @click="destroy(r.content_id, r.title)"
                                        class="text-xs text-red-500 hover:underline font-bold">삭제</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="restaurants.data.length === 0">
                            <td colspan="5" class="text-center py-12 text-gray-400">데이터가 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="flex gap-1 justify-center">
                <Link v-for="link in restaurants.links" :key="link.label"
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
