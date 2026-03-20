<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    post: Object
});
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
                <span class="mx-2">조회수 0</span>
            </div>

            <div class="prose max-w-none text-gray-800 leading-relaxed whitespace-pre-wrap">
                {{ post.content }}
            </div>

            <div class="mt-12 flex justify-between items-center pt-6 border-t border-gray-100">
                <Link :href="route('home')" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    &larr; 목록으로 돌아가기
                </Link>
                
                <div v-if="$page.props.auth.user && $page.props.auth.user.id === post.user_id" class="space-x-4">
                    <!-- edit / delete goes here later -->
                </div>
            </div>
        </div>
    </MainLayout>
</template>
