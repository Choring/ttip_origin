<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-[15px] font-bold text-gray-900 flex items-center">
        <span class="text-red-500 mr-2 text-lg">🔥</span> 실시간 인기 게시글
      </h3>
      <Link :href="route('popular')" class="text-[11px] font-bold text-gray-400 hover:text-primary transition-colors">전체보기</Link>
    </div>
    
    <ul class="space-y-4" v-if="page.props.popular_posts && page.props.popular_posts.length > 0">
      <li v-for="(post, index) in page.props.popular_posts" :key="post.id" class="group">
        <Link :href="route('posts.show', post.id)" class="flex items-start space-x-3">
          <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center text-[10px] font-black rounded-lg bg-gray-50 text-gray-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
            {{ index + 1 }}
          </span>
          <div class="min-w-0">
            <p class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-primary transition-colors tracking-tight">
              {{ post.title }}
            </p>
            <div class="flex items-center mt-1 space-x-2">
              <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-1.5 py-0.5 rounded-md">HOT</span>
              <span class="text-[10px] font-semibold text-gray-400">{{ post.score.toLocaleString() }} 점</span>
            </div>
          </div>
        </Link>
      </li>
    </ul>
    <div v-else class="text-xs text-gray-400 text-center py-4 font-medium">인기 게시글 데이터가 없습니다.</div>
  </div>
</template>
