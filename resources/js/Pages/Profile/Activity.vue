<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    tab: String,
    myPosts: Object,
    bookmarkedPosts: Object,
    stats: Object,
});

const items = computed(() => {
    if (props.tab === 'bookmarks') {
        return props.bookmarkedPosts;
    }
    return props.myPosts;
});

const switchTab = (tab) => {
    router.get(route('profile.activity'), { tab }, {
        preserveScroll: true,
        replace: true,
    });
};

const goToPage = (url) => {
    if (!url) return;
    router.get(url, { tab: props.tab }, { preserveScroll: true });
};
</script>

<template>
    <Head title="내 활동" />

    <MainLayout>
        <div class="py-2 sm:py-6">
            <!-- 헤더 -->
            <div class="mb-6 px-4 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-800">내 활동</h2>
                    <p class="text-sm text-gray-500 mt-1">내가 쓴 글과 북마크한 게시글을 관리하세요.</p>
                </div>
                <Link
                    :href="route('profile.edit')"
                    class="text-sm text-gray-400 hover:text-gray-600 font-semibold transition-colors flex items-center gap-1"
                >
                    ← 마이페이지
                </Link>
            </div>

            <!-- 통계 카드 -->
            <div class="grid grid-cols-2 gap-3 mb-6 px-0">
                <button
                    @click="switchTab('posts')"
                    class="rounded-2xl p-4 text-left transition-all border-2"
                    :class="tab === 'posts'
                        ? 'bg-indigo-50 border-indigo-400 shadow-sm'
                        : 'bg-white border-gray-100 hover:border-gray-200'"
                >
                    <p class="text-xs font-bold text-gray-400 mb-1">내가 쓴 글</p>
                    <p class="text-2xl font-black" :class="tab === 'posts' ? 'text-indigo-600' : 'text-gray-800'">
                        {{ (stats?.postCount || 0).toLocaleString() }}
                    </p>
                </button>
                <button
                    @click="switchTab('bookmarks')"
                    class="rounded-2xl p-4 text-left transition-all border-2"
                    :class="tab === 'bookmarks'
                        ? 'bg-amber-50 border-amber-400 shadow-sm'
                        : 'bg-white border-gray-100 hover:border-gray-200'"
                >
                    <p class="text-xs font-bold text-gray-400 mb-1">북마크</p>
                    <p class="text-2xl font-black" :class="tab === 'bookmarks' ? 'text-amber-500' : 'text-gray-800'">
                        {{ (stats?.bookmarkCount || 0).toLocaleString() }}
                    </p>
                </button>
            </div>

            <!-- 탭 헤더 -->
            <div class="flex border-b border-gray-200 mb-4">
                <button
                    @click="switchTab('posts')"
                    class="px-5 py-3 text-sm font-bold transition-colors border-b-2 -mb-px"
                    :class="tab === 'posts'
                        ? 'border-indigo-600 text-indigo-600'
                        : 'border-transparent text-gray-400 hover:text-gray-600'"
                >
                    ✏️ 내가 쓴 글
                </button>
                <button
                    @click="switchTab('bookmarks')"
                    class="px-5 py-3 text-sm font-bold transition-colors border-b-2 -mb-px"
                    :class="tab === 'bookmarks'
                        ? 'border-amber-500 text-amber-500'
                        : 'border-transparent text-gray-400 hover:text-gray-600'"
                >
                    🔖 북마크
                </button>
            </div>

            <!-- 게시글 리스트 -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <!-- 아이템 있을 때 -->
                <template v-if="items && items.data && items.data.length > 0">
                    <ul class="divide-y divide-gray-50">
                        <li v-for="post in items.data" :key="post.id">
                            <Link
                                :href="route('posts.show', post.id)"
                                class="flex items-start gap-3 px-4 py-4 hover:bg-gray-50 transition-colors group"
                            >
                                <!-- 카테고리 배지 -->
                                <span class="mt-0.5 flex-shrink-0 text-[10px] font-black px-2 py-1 rounded-lg bg-indigo-50 text-indigo-500 whitespace-nowrap">
                                    {{ post.category || '일반' }}
                                </span>

                                <!-- 제목 + 메타 -->
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors leading-snug line-clamp-2"
                                        :class="{ 'opacity-40 line-through': post.isHidden }"
                                    >
                                        {{ post.title }}
                                        <span v-if="post.isHidden" class="text-[10px] font-black text-red-400 no-underline">[블라인드]</span>
                                    </p>
                                    <div class="flex items-center gap-3 mt-1.5 text-[11px] text-gray-400 font-medium">
                                        <span>{{ post.createdAt }}</span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                            {{ post.likes }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                            {{ post.comments }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            {{ post.views }}
                                        </span>
                                    </div>
                                </div>

                                <!-- 화살표 -->
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400 flex-shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </li>
                    </ul>

                    <!-- 페이지네이션 -->
                    <div v-if="items.last_page > 1" class="flex items-center justify-center gap-1 p-4 border-t border-gray-50">
                        <button
                            v-for="link in items.links"
                            :key="link.label"
                            @click="goToPage(link.url)"
                            :disabled="!link.url"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                            :class="link.active
                                ? 'bg-indigo-600 text-white'
                                : link.url
                                    ? 'text-gray-500 hover:bg-gray-100'
                                    : 'text-gray-300 cursor-not-allowed'"
                            v-html="link.label"
                        />
                    </div>
                </template>

                <!-- 빈 상태 -->
                <div v-else class="text-center py-16 px-4">
                    <div class="text-5xl mb-3">{{ tab === 'bookmarks' ? '🔖' : '✏️' }}</div>
                    <p class="text-sm font-bold text-gray-500 mb-1">
                        {{ tab === 'bookmarks' ? '북마크한 게시글이 없어요' : '작성한 게시글이 없어요' }}
                    </p>
                    <p class="text-xs text-gray-400 mb-5">
                        {{ tab === 'bookmarks' ? '마음에 드는 글을 북마크해 보세요!' : '첫 번째 글을 작성해 보세요!' }}
                    </p>
                    <Link
                        v-if="tab === 'posts'"
                        :href="route('posts.create')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-full hover:bg-indigo-700 transition-colors"
                    >
                        ✏️ 글 쓰러 가기
                    </Link>
                    <Link
                        v-else
                        :href="route('home')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white text-xs font-bold rounded-full hover:bg-amber-600 transition-colors"
                    >
                        🏠 홈으로 가기
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
