<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';

const props = defineProps({
    tag:        { type: String, required: true },
    posts:      { type: Array,  default: () => [] },
    pagination: { type: Object, default: () => ({}) },
});

const loadMore = () => {
    if (props.pagination.current_page < props.pagination.last_page) {
        router.get(route('tags.show', props.tag), { page: props.pagination.current_page + 1 }, {
            preserveScroll: true,
            preserveState: true,
        });
    }
};
</script>

<template>
    <Head>
        <title>#{{ tag }} 태그 게시글 - ttip</title>
        <meta head-key="description" name="description" :content="`ttip에서 #${tag} 태그가 달린 게시글을 모아보세요.`" />
    </Head>

    <MainLayout>
        <!-- 헤더 -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <Link :href="route('home')" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">홈</Link>
                <span class="text-xs text-gray-300">/</span>
                <span class="text-xs text-gray-400">태그</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black text-gray-900">#{{ tag }}</span>
                <span class="text-sm text-gray-400 font-medium">{{ pagination.total?.toLocaleString() }}개의 게시글</span>
            </div>
        </div>

        <!-- 게시글 목록 -->
        <div class="space-y-4">
            <SummaryCard
                v-for="post in posts"
                :key="post.id"
                :id="post.id"
                :author-name="post.authorName"
                :author-avatar="post.authorAvatar"
                :author-tier-name="post.authorTierName"
                :author-tier-icon="post.authorTierIcon"
                :time-ago="post.timeAgo"
                :category="post.category"
                :category-slug="post.categorySlug"
                :tags="post.tags"
                :title="post.title"
                :summary="post.summary"
                :likes="post.likes"
                :comments="post.comments"
                :views="post.views"
                :is-bookmarked="post.isBookmarked"
            />
        </div>

        <!-- 빈 상태 -->
        <div v-if="posts.length === 0" class="text-center py-16 text-gray-400">
            <div class="text-4xl mb-3">🏷️</div>
            <p class="font-bold text-gray-500">#{{ tag }} 태그 게시글이 없습니다.</p>
            <Link :href="route('home')" class="mt-4 inline-block text-sm text-primary hover:underline font-medium">
                전체 피드 보기
            </Link>
        </div>

        <!-- 더보기 버튼 -->
        <div v-if="pagination.current_page < pagination.last_page" class="mt-8 flex justify-center">
            <button
                @click="loadMore"
                class="px-8 py-3 bg-white border border-gray-200 text-gray-700 rounded-full font-bold text-sm hover:bg-gray-50 shadow-sm transition-all"
            >
                더보기
            </button>
        </div>
    </MainLayout>
</template>
