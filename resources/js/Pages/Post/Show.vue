<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CommentItem from '@/Components/CommentItem.vue';
import ShareButtons from '@/Components/ShareButtons.vue';
import { computed } from 'vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
    post: Object,
    isLiked: Boolean,
    isBookmarked: Boolean,
});


const page = usePage();
const user = page.props.auth.user;

const commentsTree = computed(() => {
    const flat = props.post.comments || [];
    const tree = [];
    const lookup = {};

    flat.forEach(comment => {
        lookup[comment.id] = { ...comment, children: [] };
    });

    flat.forEach(comment => {
        if (comment.parent_id === null) {
            tree.push(lookup[comment.id]);
        } else if (lookup[comment.parent_id]) {
            lookup[comment.parent_id].children.push(lookup[comment.id]);
        }
    });

    return tree;
});

const form = useForm({
    content: '',
});

const submitComment = () => {
    form.post(route('comments.store', { post: props.post.id }), {
        preserveScroll: true,
        onSuccess: () => form.reset('content'),
    });
};

const getLabel = (key) => {
    const labels = {
        'location': '위치',
        'price': '가격대',
        'waiting': '웨이팅',
        'parking': '주차',
        'outlets': '콘센트',
        'wifi': '와이파이',
        'solo_seats': '1인석 여부',
        'fee': '월회비',
        'hours': '운영시간',
        'facilities': '시설',
        'wage': '시급',
        'industry': '업종'
    };
    return labels[key] || key;
};

const { showToast } = useToast();

// 게시글 절대 URL
const postUrl = computed(() => {
    try { return route('posts.show', props.post.id); }
    catch { return typeof window !== 'undefined' ? window.location.href : ''; }
});

// OG description용 요약 텍스트
const descriptionText = computed(() =>
    Array.isArray(props.post.summary) ? props.post.summary.join(' ') : (props.post.summary || '')
);
</script>

<template>
    <Head>
        <title>{{ post?.title ? `${post.title} - ttip` : '로딩 중' }}</title>
        <meta v-if="post?.summary" head-key="description" name="description" :content="descriptionText">
        <!-- Open Graph 태그 -->
        <meta property="og:type"        content="article" />
        <meta property="og:site_name"   content="ttip" />
        <meta property="og:title"       :content="post?.title" />
        <meta property="og:description" :content="descriptionText" />
        <meta property="og:url"         :content="postUrl" />
        <meta v-if="post?.card_image_url" property="og:image" :content="post.card_image_url" />
        <!-- Twitter Card -->
        <meta name="twitter:card"        :content="post?.card_image_url ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title"       :content="post?.title" />
        <meta name="twitter:description" :content="descriptionText" />
        <meta v-if="post?.card_image_url" name="twitter:image" :content="post.card_image_url" />
    </Head>

    <MainLayout>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8" v-if="post">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span v-if="post.type === 'ad'" class="bg-red-500 text-white px-3 py-1 rounded-md text-xs font-black shadow-sm mr-2 animate-pulse">[광고]</span>
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-extrabold shadow-sm">{{ post.category?.name || '분류 없음' }}</span>
                    <span v-for="t in post.tags" :key="t" class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold shadow-sm border border-gray-200">#{{ t }}</span>
                </div>
                
                <!-- 상단 액션 버튼 (공유 / 북마크) -->
                <div class="flex items-center gap-1">
                    <div v-if="user">
                        <Link 
                            :href="route('posts.bookmark', post.id)" 
                            method="post" 
                            as="button" 
                            preserve-scroll
                            class="p-2 transition-all rounded-full hover:bg-amber-50 group"
                            :title="isBookmarked ? '북마크 취소' : '북마크 저장'"
                        >
                            <svg 
                                class="w-6 h-6 transition-colors" 
                                :class="isBookmarked ? 'text-amber-500' : 'text-gray-300 group-hover:text-amber-400'" 
                                viewBox="0 0 24 24" 
                                :fill="isBookmarked ? 'currentColor' : 'none'" 
                                stroke="currentColor" 
                                stroke-width="2"
                            >
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>

            <h1 class="text-3xl font-bold mb-4 text-gray-800">{{ post.title }}</h1>
            
            <div class="flex items-center text-gray-500 text-sm mb-6 border-b pb-4">
                <div class="flex items-center space-x-1 font-bold text-gray-700 mr-2">
                    <span v-if="post.user?.tier?.icon_url" :title="post.user.tier.name" class="text-xs">{{ post.user.tier.icon_url }}</span>
                    <span>{{ post.user?.name || '알 수 없음' }}</span>
                </div>
                <span>•</span>
                <span class="mx-2">{{ new Date(post.created_at).toLocaleDateString() }}</span>
                <span>•</span>
                <span class="mx-2">조회수 {{ post.view_count || 0 }}</span>
            </div>

            <!-- 정형 정보 카드 (Information Card) -->
            <div v-if="post.extra_info && Object.keys(post.extra_info).length > 0" class="mb-8 bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-6 border-2 border-indigo-100 shadow-sm relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-100 rounded-full opacity-30 transform scale-150"></div>
                <div class="relative z-10">
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        <h3 class="font-black text-indigo-900 tracking-tight">핵심 정보 요약</h3>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                        <div v-for="(value, key) in post.extra_info" :key="key" class="flex flex-col">
                            <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">{{ getLabel(key) }}</span>
                            <span class="text-sm font-bold text-gray-800 break-words">{{ value || '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="prose prose-indigo max-w-none text-gray-800 leading-relaxed" v-html="post.content">
            </div>

            <!-- Like Action Bar -->
            <div class="mt-8 pb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                <Link :href="route('home')" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors order-2 sm:order-1">
                    &larr; 목록으로 돌아가기
                </Link>
                
                <div class="order-1 sm:order-2 flex-1 flex justify-center">
                    <Link v-if="user" :href="route('posts.like', post.id)" method="post" as="button" preserve-scroll class="flex items-center space-x-2 px-8 py-3.5 rounded-full font-black text-lg transition-all transform hover:-translate-y-0.5 active:translate-y-0 shadow-md border-2" :class="isLiked ? 'bg-indigo-50 border-indigo-600 text-indigo-700' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'">
                        <span>띱 👍</span>
                        <span class="ml-1 bg-white px-2 py-0.5 rounded-full text-base border" :class="isLiked ? 'border-indigo-200' : 'border-gray-100'">{{ post.likes_count || 0 }}</span>
                    </Link>
                    <div v-else class="flex items-center space-x-2 px-8 py-3.5 rounded-full font-black text-lg bg-gray-50 border-2 border-gray-100 text-gray-400 cursor-not-allowed">
                        <span>띱 👍</span>
                        <span class="ml-1 bg-white px-2 py-0.5 rounded-full text-base border border-gray-100">{{ post.likes_count || 0 }}</span>
                    </div>
                </div>

                <div v-if="user && user.id === post.user_id" class="flex space-x-3 order-3">
                    <Link :href="route('posts.edit', post.id)" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg text-sm font-bold border border-gray-200 transition-colors shadow-sm">수정</Link>
                    <Link :href="route('posts.destroy', post.id)" method="delete" as="button" type="button" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-bold border border-red-100 transition-colors shadow-sm" preserve-scroll @click="(e) => { if(!confirm('이 게시글을 정말 삭제하시겠습니까?\n달려있는 댓글도 연쇄적으로 지워집니다.')) e.preventDefault() }">삭제</Link>
                </div>
                <div v-else class="order-3 w-[100px] hidden sm:block"></div>
            </div>

            <!-- 공유 버튼 섹션 -->
            <div class="py-5 border-t border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">이 글 공유하기</span>
                <ShareButtons
                    :post-id="post.id"
                    :title="post.title"
                    :summary="post.summary"
                    :image-url="post.card_image_url"
                />
            </div>

            <!-- Comments Section -->
            <div class="mt-12 pt-8 border-t border-gray-100">
                <h3 class="text-xl font-bold mb-6 flex items-center space-x-2">
                    <span>댓글</span> 
                    <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md text-sm">{{ post.comments?.length || 0 }}</span>
                </h3>

                <!-- Main Comment Form -->
                <div v-if="user" class="mb-10">
                    <form @submit.prevent="submitComment" class="relative">
                        <textarea v-model="form.content" rows="3" placeholder="댓글을 자유롭게 남겨보세요..." class="w-full p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none shadow-sm transition-shadow" required></textarea>
                        <div class="absolute bottom-3 right-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition shadow-sm" :disabled="form.processing">
                                댓글 작성 (+2P)
                            </button>
                        </div>
                    </form>
                </div>
                <div v-else class="mb-10 p-5 bg-gray-50 rounded-xl text-center text-sm font-medium text-gray-500 border border-gray-200">
                    댓글을 작성하려면 로그인이 필요합니다.
                </div>

                <!-- Comments List -->
                <div class="space-y-6">
                    <CommentItem v-for="comment in commentsTree" :key="comment.id" :comment="comment" :postId="post.id" />
                    
                    <div v-if="commentsTree.length === 0" class="text-center text-gray-400 py-10">
                        첫 댓글의 주인공이 되어보세요!
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
