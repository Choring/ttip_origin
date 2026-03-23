<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CommentItem from '@/Components/CommentItem.vue';
import { computed } from 'vue';

const props = defineProps({
    post: Object
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
</script>

<template>
    <Head :title="post?.title || '로딩 중'" />

    <MainLayout>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8" v-if="post">
            <h1 class="text-3xl font-bold mb-4">{{ post.title }}</h1>
            
            <div class="flex items-center text-gray-500 text-sm mb-8 border-b pb-4">
                <span class="font-bold text-gray-700 mr-2">{{ post.user?.name || '알 수 없음' }}</span>
                <span>•</span>
                <span class="mx-2">{{ new Date(post.created_at).toLocaleDateString() }}</span>
                <span>•</span>
                <span class="mx-2">조회수 {{ post.view_count || 0 }}</span>
            </div>

            <div class="prose max-w-none text-gray-800 leading-relaxed whitespace-pre-wrap">
                {{ post.content }}
            </div>

            <div class="mt-8 flex justify-between items-center">
                <Link :href="route('home')" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                    &larr; 목록으로 돌아가기
                </Link>
                
                <div v-if="user && user.id === post.user_id" class="space-x-4">
                    <!-- edit / delete goes here later -->
                </div>
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
