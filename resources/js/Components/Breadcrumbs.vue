<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
        // Example: [{ label: '홈', href: '/' }, { label: '카테고리', href: '/?category=slug' }, { label: '게시글' }]
    }
});
</script>

<template>
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-xs font-bold tracking-tight">
            <li v-for="(item, index) in items" :key="index" class="flex items-center">
                <!-- Separator (Home 아이콘 대신 텍스트일 경우 첫 번째 요소 제외하고 표시) -->
                <svg v-if="index > 0" class="flex-shrink-0 h-4 w-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>

                <!-- Link if href exists and not last item -->
                <Link 
                    v-if="item.href && index < items.length - 1" 
                    :href="item.href"
                    class="text-gray-400 hover:text-indigo-600 transition-colors uppercase"
                >
                    {{ item.label }}
                </Link>

                <!-- Plain text if last item or no href -->
                <span 
                    v-else 
                    class="text-indigo-500 font-extrabold line-clamp-1 max-w-[200px]"
                >
                    {{ item.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>
