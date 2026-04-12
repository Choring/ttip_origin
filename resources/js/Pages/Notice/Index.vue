<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  posts: Object,
});
</script>

<template>
  <Head title="ttip - 공지사항" />

  <MainLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">공지사항</h1>
            <p class="text-sm text-gray-500 mt-1">ttip의 새로운 소식과 안내를 확인하세요.</p>
        </div>
        <div class="text-4xl">📢</div>
      </div>

      <div v-if="posts.data && posts.data.length > 0" class="flex flex-col gap-6">
          <SummaryCard
            v-for="post in posts.data"
            :key="post.id"
            :id="post.id"
            :author-name="post.authorName"
            :author-avatar="post.authorAvatar"
            :time-ago="post.timeAgo"
            :category="post.category"
            :tags="post.tags"
            :title="post.title"
            :summary="post.summary"
            :likes="post.likes"
            :views="post.views"
            :type="'notice'"
            :is-pinned="post.is_pinned"
          />
          
          <!-- Pagination -->
          <div v-if="posts.links && posts.links.length > 3" class="mt-6 flex justify-center">
              <nav class="flex items-center space-x-1">
                  <template v-for="(link, k) in posts.links" :key="k">
                      <div v-if="link.url === null" class="px-3 py-2 text-sm text-gray-400 cursor-default" v-html="link.label"></div>
                      <Link 
                          v-else 
                          :href="link.url" 
                          class="px-3 py-2 text-sm rounded-lg transition-colors" 
                          :class="link.active ? 'bg-indigo-600 text-white font-bold' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100'" 
                          v-html="link.label"
                      ></Link>
                  </template>
              </nav>
          </div>
      </div>

      <div v-if="!posts.data || posts.data.length === 0" class="text-center py-16 text-gray-500 bg-white border border-gray-200 rounded-3xl shadow-sm">
        등록된 공지사항이 아직 없습니다.
      </div>
    </div>
  </MainLayout>
</template>
