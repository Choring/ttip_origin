<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col shadow-xl z-10">
            <div class="h-16 flex items-center justify-center border-b border-gray-800">
                <span class="text-xl font-bold tracking-wider">🛠️ Admin Panel</span>
            </div>
            <nav class="flex-1 px-4 py-8 space-y-3">
                <Link :href="route('admin.dashboard')" class="block px-4 py-2.5 rounded-lg transition-colors font-medium text-sm" :class="{'bg-indigo-600 text-white shadow': $page.url === '/admin', 'text-gray-400 hover:bg-gray-800 hover:text-white': $page.url !== '/admin'}">
                    대시보드 홈
                </Link>
                <Link :href="route('admin.users.index')" class="block px-4 py-2.5 rounded-lg transition-colors font-medium text-sm" :class="{'bg-indigo-600 text-white shadow': $page.url.startsWith('/admin/users'), 'text-gray-400 hover:bg-gray-800 hover:text-white': !$page.url.startsWith('/admin/users')}">
                    회원 관리
                </Link>
                <Link :href="route('admin.categories.index')" class="block px-4 py-2.5 rounded-lg transition-colors font-medium text-sm" :class="{'bg-indigo-600 text-white shadow': $page.url.startsWith('/admin/categories'), 'text-gray-400 hover:bg-gray-800 hover:text-white': !$page.url.startsWith('/admin/categories')}">
                    카테고리 관리
                </Link>
                <Link :href="route('admin.notices.index')" class="block px-4 py-2.5 rounded-lg transition-colors font-medium text-sm" :class="{'bg-indigo-600 text-white shadow': $page.url.startsWith('/admin/notices'), 'text-gray-400 hover:bg-gray-800 hover:text-white': !$page.url.startsWith('/admin/notices')}">
                    📢 공지사항 관리
                </Link>
                <Link :href="route('admin.posts.index')" class="block px-4 py-2.5 rounded-lg transition-colors font-medium text-sm" :class="{'bg-indigo-600 text-white shadow': $page.url.startsWith('/admin/posts'), 'text-gray-400 hover:bg-gray-800 hover:text-white': !$page.url.startsWith('/admin/posts')}">
                    게시글 관리
                </Link>
                <Link :href="route('home')" class="block px-4 py-2.5 rounded-lg text-gray-500 hover:bg-gray-800 hover:text-gray-200 transition-colors mt-12 text-sm">
                    &larr; 사이트로 돌아가기
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col relative overflow-hidden">
            <header class="h-16 bg-white shadow-sm flex items-center px-8 justify-between border-b border-gray-200 z-0">
                <h2 class="font-semibold text-gray-800 text-lg">관리자 모드</h2>
                <div class="text-sm font-medium text-gray-600 bg-gray-100 px-4 py-1.5 rounded-full">
                    {{ $page.props.auth.user.name }} (<span class="text-indigo-600">{{ $page.props.auth.user.role }}</span>)
                </div>
            </header>
            <div class="p-8 overflow-y-auto flex-1">
                <slot />
            </div>
        </main>
    </div>
</template>
