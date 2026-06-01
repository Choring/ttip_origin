<template>
  <Head title="배너 관리 - Admin" />
  <AdminLayout>
    <div class="p-6">
      <!-- 헤더 -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-black text-gray-900">배너 관리</h1>
        <Link :href="route('admin.banners.create')"
          class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition-colors">
          + 배너 추가
        </Link>
      </div>

      <!-- 배너 목록 -->
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-3 text-left font-bold text-gray-600">순서</th>
              <th class="px-4 py-3 text-left font-bold text-gray-600">미리보기</th>
              <th class="px-4 py-3 text-left font-bold text-gray-600">제목</th>
              <th class="px-4 py-3 text-left font-bold text-gray-600">기간</th>
              <th class="px-4 py-3 text-left font-bold text-gray-600">상태</th>
              <th class="px-4 py-3 text-left font-bold text-gray-600">관리</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="banner in banners" :key="banner.id" class="hover:bg-gray-50 transition-colors">
              <!-- 순서 -->
              <td class="px-4 py-3 text-gray-500 font-mono">{{ banner.sort_order }}</td>

              <!-- 미리보기 이미지 -->
              <td class="px-4 py-3">
                <img :src="banner.image_url" :alt="banner.title"
                  class="w-24 h-14 object-cover rounded-lg bg-gray-100"
                  @error="e => e.target.style.display='none'"
                />
              </td>

              <!-- 제목/부제목 -->
              <td class="px-4 py-3">
                <p class="font-bold text-gray-900">{{ banner.title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ banner.subtitle }}</p>
                <p v-if="banner.link_url" class="text-xs text-indigo-400 mt-0.5 truncate max-w-xs">🔗 {{ banner.link_url }}</p>
              </td>

              <!-- 기간 -->
              <td class="px-4 py-3 text-gray-500 text-xs">
                <span v-if="banner.started_at || banner.ended_at">
                  {{ banner.started_at ? formatDate(banner.started_at) : '상시' }}
                  ~
                  {{ banner.ended_at ? formatDate(banner.ended_at) : '상시' }}
                </span>
                <span v-else class="text-gray-400">상시 노출</span>
              </td>

              <!-- 상태 토글 -->
              <td class="px-4 py-3">
                <button @click="toggleActive(banner)"
                  class="px-3 py-1 rounded-full text-xs font-bold transition-colors"
                  :class="banner.is_active
                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                >
                  {{ banner.is_active ? '활성' : '비활성' }}
                </button>
              </td>

              <!-- 수정/삭제 -->
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <Link :href="route('admin.banners.edit', banner.id)"
                    class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-100 transition-colors">
                    수정
                  </Link>
                  <button @click="deleteBanner(banner)"
                    class="px-3 py-1 bg-red-50 text-red-500 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors">
                    삭제
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="banners.length === 0">
              <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                등록된 배너가 없습니다.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
  banners: { type: Array, default: () => [] },
});

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('ko-KR', { year: 'numeric', month: '2-digit', day: '2-digit' });
};

const toggleActive = (banner) => {
  router.patch(route('admin.banners.toggleActive', banner.id), {}, { preserveScroll: true });
};

const deleteBanner = (banner) => {
  if (!confirm(`"${banner.title}" 배너를 삭제할까요?`)) return;
  router.delete(route('admin.banners.destroy', banner.id));
};
</script>
