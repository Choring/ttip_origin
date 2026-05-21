<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CommentItem from '@/Components/CommentItem.vue';
import ShareButtons from '@/Components/ShareButtons.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import LoginModal from '@/Components/LoginModal.vue';
import RelatedPosts from '@/Components/RelatedPosts.vue';
import { ref, computed } from 'vue';
import { useToast } from '@/Composables/useToast';
import ReportModal from '@/Components/ReportModal.vue';
import TierUpgradeModal from '@/Components/TierUpgradeModal.vue';
import axios from 'axios';

const props = defineProps({
    post: Object,
    isLiked: Boolean,
    isBookmarked: Boolean,
    relatedPosts: Array,
    likedCommentIds: Array,
});

const page = usePage();
const user = page.props.auth.user;
const { showPointToast } = useToast();

// ── 댓글 로컬 상태 ──────────────────────────────────────────────
const localComments      = ref([...(props.post.comments || [])]);
const localLikedCommentIds = ref([...(props.likedCommentIds || [])]);

const commentsTree = computed(() => {
    const flat = localComments.value;
    const tree = [];
    const lookup = {};

    flat.forEach(comment => {
        lookup[comment.id] = {
            ...comment,
            isLiked: localLikedCommentIds.value.includes(comment.id),
            children: [],
        };
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

// ── 댓글 이벤트 핸들러 ──────────────────────────────────────────
const onCommentAdded = (comment) => {
    localComments.value.push(comment);
};

const onCommentUpdated = ({ id, content }) => {
    const idx = localComments.value.findIndex(c => c.id === id);
    if (idx !== -1) localComments.value[idx] = { ...localComments.value[idx], content };
};

const onCommentDeleted = ({ id, replyIds }) => {
    const removeIds = new Set([id, ...(replyIds || [])]);
    localComments.value = localComments.value.filter(c => !removeIds.has(c.id));
};

const onCommentLiked = ({ id, liked, likes_count }) => {
    const idx = localComments.value.findIndex(c => c.id === id);
    if (idx !== -1) localComments.value[idx] = { ...localComments.value[idx], likes_count };
    if (liked) {
        if (!localLikedCommentIds.value.includes(id)) localLikedCommentIds.value.push(id);
    } else {
        localLikedCommentIds.value = localLikedCommentIds.value.filter(cid => cid !== id);
    }
};

// ── 댓글 작성 ────────────────────────────────────────────────────
const commentContent  = ref('');
const isSubmitting    = ref(false);

const submitComment = async () => {
    if (!commentContent.value.trim() || isSubmitting.value) return;
    isSubmitting.value = true;
    try {
        const res = await axios.post(route('comments.store', { post: props.post.id }), {
            content: commentContent.value,
        });
        localComments.value.push(res.data.comment);
        if (res.data.point_gain) showPointToast(res.data.point_gain);
        if (res.data.tier_upgrade) tierUpgradeData.value = res.data.tier_upgrade;
        commentContent.value = '';
    } catch (e) {
        console.error(e);
    } finally {
        isSubmitting.value = false;
    }
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

// 신고 모달
const showReportModal = ref(false);

// 티어 승급 모달 (AJAX 댓글 작성 시)
const tierUpgradeData = ref(null);

// 비로그인 유도 모달 관련
const showLoginModal = ref(false);
const loginModalText = ref({
    title: '',
    description: ''
});

const openLoginModal = (type) => {
    if (type === 'like') {
        loginModalText.value = {
            title: '도움이 된 정보였나요? 👍',
            description: '로그인하고 작성자에게 따뜻한 띱을 전해보세요!'
        };
    } else if (type === 'bookmark') {
        loginModalText.value = {
            title: '이 꿀팁을 저장할까요? 🔖',
            description: '띱(TTIP)의 모든 정보를 모아두고 언제든 꺼내보세요!'
        };
    } else {
        loginModalText.value = {
            title: '다시 오신 것을 환영해요!',
            description: '오늘도 유용한 팁을 나누어 볼까요?'
        };
    }
    showLoginModal.value = true;
};

// JSON-LD (Schema.org) for Article
const jsonLdArticle = computed(() => {
    return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": props.post?.title,
        "image": props.post?.card_image_url ? [props.post.card_image_url] : [],
        "datePublished": props.post?.created_at,
        "dateModified": props.post?.updated_at || props.post?.created_at,
        "author": [{
            "@type": "Person",
            "name": props.post?.user?.name || "ttip"
        }]
    });
});

// JSON-LD (Schema.org) for BreadcrumbList
const jsonLdBreadcrumb = computed(() => {
    return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "홈",
                "item": route('home')
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": props.post?.category?.name || "분류 없음",
                "item": route('home', { category: props.post?.category?.slug || 'all' })
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": props.post?.title
            }
        ]
    });
});
</script>

<template>
    <Head>
        <title>{{ post?.title ? `${post.title} - ttip` : '로딩 중' }}</title>
        <meta head-key="description" name="description" :content="descriptionText">
        
        <!-- Open Graph 태그 -->
        <meta head-key="og:type" property="og:type" content="article" />
        <meta head-key="og:site_name" property="og:site_name" content="ttip" />
        <meta head-key="og:title" property="og:title" :content="post?.title ? `${post.title} - ttip` : 'ttip'" />
        <meta head-key="og:description" property="og:description" :content="descriptionText" />
        <meta head-key="og:url" property="og:url" :content="postUrl" />
        <meta v-if="post?.card_image_url" head-key="og:image" property="og:image" :content="post.card_image_url" />
        
        <!-- Twitter Card -->
        <meta head-key="twitter:card" name="twitter:card" :content="post?.card_image_url ? 'summary_large_image' : 'summary'" />
        <meta head-key="twitter:title" name="twitter:title" :content="post?.title ? `${post.title} - ttip` : 'ttip'" />
        <meta head-key="twitter:description" name="twitter:description" :content="descriptionText" />
        <meta v-if="post?.card_image_url" head-key="twitter:image" name="twitter:image" :content="post.card_image_url" />
        
        <!-- Canonical URL -->
        <link head-key="canonical" rel="canonical" :href="postUrl" />

        <!-- JSON-LD Structured Data -->
        <component :is="'script'" type="application/ld+json" v-html="jsonLdArticle"></component>
        <component :is="'script'" type="application/ld+json" v-html="jsonLdBreadcrumb"></component>
    </Head>

    <MainLayout>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8" v-if="post">
            <!-- Breadcrumbs -->
            <Breadcrumbs :items="[
                { label: '홈', href: route('home') },
                { label: post.category?.name || '분류 없음', href: route('home', { category: post.category?.slug || 'all' }) },
                { label: post.title }
            ]" />

            <div class="mb-4">
                <!-- 카테고리 + 북마크 버튼 -->
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span v-if="post.type === 'ad'" class="bg-red-500 text-white px-3 py-1 rounded-md text-xs font-black shadow-sm animate-pulse">[광고]</span>
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-extrabold shadow-sm">{{ post.category?.name || '분류 없음' }}</span>
                    </div>
                    <!-- 북마크 버튼 -->
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
                    <!-- 비로그인 북마크 버튼 -->
                    <div v-else>
                        <button
                            @click="openLoginModal('bookmark')"
                            class="p-2 transition-all rounded-full hover:bg-amber-50 group"
                            title="북마크 저장"
                        >
                            <svg
                                class="w-6 h-6 text-gray-300 group-hover:text-amber-400 transition-colors"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- 태그 (별도 줄, flex-wrap) -->
                <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-1.5">
                    <span v-for="t in post.tags" :key="t" class="bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full text-xs font-semibold border border-gray-200">#{{ t }}</span>
                </div>
            </div>

            <h1 class="text-3xl font-bold mb-4 text-gray-800">{{ post.title }}</h1>
            
            <div class="mb-6 border-b pb-4">
                <!-- 작성자 -->
                <div class="flex items-center gap-1.5 mb-1.5">
                    <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-red-500 font-bold text-xs flex-shrink-0">
                        {{ (post.user?.name || '?').substring(0, 1) }}
                    </div>
                    <span class="text-sm font-bold text-gray-800">{{ post.user?.name || '알 수 없음' }}</span>
                    <span v-if="post.user?.tier?.icon_url" class="text-xs" :title="post.user.tier.name">{{ post.user.tier.icon_url }}</span>
                </div>
                <!-- 날짜 + 조회수 -->
                <div class="flex items-center justify-between text-xs text-gray-400 pl-0.5">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ new Date(post.created_at).toLocaleDateString('ko-KR', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        {{ (post.view_count || 0).toLocaleString() }}
                    </span>
                </div>
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


            <div class="prose prose-indigo prose-p:my-0 prose-li:my-0 max-w-none text-gray-800 leading-normal" v-html="post.content">
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
                    <!-- 비로그인 띱 버튼 -->
                    <button v-else @click="openLoginModal('like')" class="flex items-center space-x-2 px-8 py-3.5 rounded-full font-black text-lg bg-white border-2 border-gray-200 text-gray-600 hover:border-indigo-400 hover:bg-indigo-50 transition-all transform hover:-translate-y-0.5 shadow-md">
                        <span>띱 👍</span>
                        <span class="ml-1 bg-white px-2 py-0.5 rounded-full text-base border border-gray-100">{{ post.likes_count || 0 }}</span>
                    </button>
                </div>

                <div v-if="user && user.id === post.user_id" class="flex space-x-3 order-3">
                    <Link :href="route('posts.edit', post.id)" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg text-sm font-bold border border-gray-200 transition-colors shadow-sm">수정</Link>
                    <Link :href="route('posts.destroy', post.id)" method="delete" as="button" type="button" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-bold border border-red-100 transition-colors shadow-sm" preserve-scroll @click="(e) => { if(!confirm('이 게시글을 정말 삭제하시겠습니까?\n달려있는 댓글도 연쇄적으로 지워집니다.')) e.preventDefault() }">삭제</Link>
                </div>
                <!-- 비작성자 로그인 유저: 신고 버튼 -->
                <div class="order-3 w-[100px] flex justify-end">
                    <button v-if="user && user.id !== post.user_id"
                        @click="showReportModal = true"
                        class="text-xs text-gray-300 hover:text-red-400 transition font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21V5a2 2 0 012-2h9l5 5v13M9 21v-6h6v6"/>
                        </svg>
                        신고
                    </button>
                </div>
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

            <!-- 관련 게시글 추천 -->
            <RelatedPosts :posts="relatedPosts" />

            <!-- Comments Section -->
            <div class="mt-12 pt-8 border-t border-gray-100">
                <h3 class="text-xl font-bold mb-6 flex items-center space-x-2">
                    <span>댓글</span>
                    <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md text-sm">{{ localComments.length }}</span>
                </h3>

                <!-- Main Comment Form -->
                <div v-if="user" class="mb-10">
                    <form @submit.prevent="submitComment" class="relative">
                        <textarea v-model="commentContent" rows="3" placeholder="댓글을 자유롭게 남겨보세요..." class="w-full p-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none shadow-sm transition-shadow" required></textarea>
                        <div class="absolute bottom-3 right-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-bold text-sm transition shadow-sm flex items-center gap-2" :disabled="isSubmitting">
                                <svg v-if="isSubmitting" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ isSubmitting ? '작성 중...' : '댓글 작성 (+1P)' }}
                            </button>
                        </div>
                    </form>
                </div>
                <div v-else class="mb-10 p-5 bg-gray-50 rounded-xl text-center text-sm font-medium text-gray-500 border border-gray-200">
                    댓글을 작성하려면 로그인이 필요합니다.
                </div>

                <!-- Comments List -->
                <div class="space-y-6">
                    <CommentItem
                        v-for="comment in commentsTree"
                        :key="comment.id"
                        :comment="comment"
                        :postId="post.id"
                        @open-login-modal="openLoginModal"
                        @comment-added="onCommentAdded"
                        @comment-updated="onCommentUpdated"
                        @comment-deleted="onCommentDeleted"
                        @comment-liked="onCommentLiked"
                    />

                    <div v-if="commentsTree.length === 0" class="text-center text-gray-400 py-10">
                        첫 댓글의 주인공이 되어보세요!
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>

    <!-- 로그인 유도 모달 -->
    <LoginModal
        :show="showLoginModal"
        :title="loginModalText.title"
        :description="loginModalText.description"
        @close="showLoginModal = false"
    />

    <!-- 게시글 신고 모달 -->
    <ReportModal
        v-if="showReportModal"
        reportable-type="post"
        :reportable-id="post.id"
        @close="showReportModal = false"
    />

    <!-- 댓글 작성 시 티어 승급 모달 -->
    <TierUpgradeModal
        v-if="tierUpgradeData"
        :tier="tierUpgradeData"
        @close="tierUpgradeData = null"
    />
</template>
