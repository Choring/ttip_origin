<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    posts: {
        type: Array,
        required: true
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <div v-if="posts && posts.length > 0" class="mt-12 py-8 border-t border-gray-100">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-2">💡</span> 이런 글도 있어요
        </h3>

        <!-- PC: 2-column Grid / Mobile: Horizontal Scroll -->
        <div class="hidden md:grid grid-cols-2 gap-4">
            <Link 
                v-for="post in posts" 
                :key="'pc-' + post.id" 
                :href="route('posts.show', post.id)"
                class="flex gap-4 p-3 rounded-2xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100 group"
            >
                <div v-if="post.card_image_url" class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                    <img :src="post.card_image_url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="thumbnail" />
                </div>
                <div v-else class="w-20 h-20 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-indigo-200 font-black text-xs uppercase">ttip</span>
                </div>
                <div class="flex flex-col justify-center min-w-0">
                    <h4 class="font-bold text-gray-800 line-clamp-2 leading-snug group-hover:text-indigo-600 transition-colors mb-1.5">
                        {{ post.title }}
                    </h4>
                    <div class="flex items-center text-[11px] text-gray-400 gap-2">
                        <span>{{ post.user.name }}</span>
                        <span class="w-0.5 h-0.5 bg-gray-300 rounded-full"></span>
                        <span>{{ formatDate(post.created_at) }}</span>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Mobile: Horizontal Scroll -->
        <div class="md:hidden -mx-4 px-4 flex overflow-x-auto gap-3 no-scrollbar pb-2">
            <Link 
                v-for="post in posts" 
                :key="'mobile-' + post.id" 
                :href="route('posts.show', post.id)"
                class="flex-shrink-0 w-64 bg-white border border-gray-100 rounded-2xl p-3 shadow-sm active:scale-95 transition-transform"
            >
                <div class="flex gap-3">
                    <div v-if="post.card_image_url" class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0">
                        <img :src="post.card_image_url" class="w-full h-full object-cover" alt="thumbnail" />
                    </div>
                    <div v-else class="w-16 h-16 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <span class="text-indigo-200 font-black text-[10px] uppercase">ttip</span>
                    </div>
                    <div class="flex flex-col justify-center min-w-0 flex-1">
                        <h4 class="font-bold text-gray-800 line-clamp-2 text-sm leading-snug mb-1">
                            {{ post.title }}
                        </h4>
                        <p class="text-[10px] text-gray-400 font-medium">
                            {{ post.user.name }} · {{ formatDate(post.created_at) }}
                        </p>
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
