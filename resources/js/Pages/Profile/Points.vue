<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    user:      Object,
    histories: Object,
    progress:  Object,
});

const amountClass = (amount) => {
    if (amount > 0) return 'text-emerald-600 font-bold';
    return 'text-red-400 font-bold';
};

const amountText = (amount) => {
    return amount > 0 ? `+${amount.toLocaleString()}P` : `${amount.toLocaleString()}P`;
};

const typeIcon = (type) => {
    const icons = {
        'create_post':          '✍️',
        'earn_comment_effort':  '💬',
        'receive_comment':      '📩',
        'receive_like':         '👍',
        'receive_comment_like': '❤️',
        'lost_like':            '👎',
        'lost_comment_like':    '💔',
        'revoke_post':          '🗑️',
        'revoke_comment':       '🗑️',
        'spend':                '🛒',
    };
    return icons[type] || '•';
};
</script>

<template>
    <Head title="포인트 내역" />

    <MainLayout>
        <div class="space-y-5">

            <!-- 헤더 -->
            <div class="flex items-center gap-3 mb-1">
                <Link :href="route('profile.edit')"
                    class="text-gray-400 hover:text-gray-600 transition text-sm flex items-center gap-1">
                    ← 마이페이지
                </Link>
            </div>

            <!-- 포인트 현황 카드 -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-indigo-200 text-xs font-semibold uppercase tracking-wider">현재 티어</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-3xl">{{ user.tier?.icon_url || '🌱' }}</span>
                            <span class="text-xl font-black">{{ user.tier?.name || '씨앗' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-indigo-200 text-xs font-semibold">누적 획득 포인트</p>
                        <p class="text-2xl font-black mt-0.5">{{ user.total_points.toLocaleString() }}<span class="text-sm font-semibold ml-1">P</span></p>
                    </div>
                </div>

                <!-- 잔액 -->
                <div class="bg-white/10 rounded-xl px-4 py-3 flex justify-between items-center mb-4">
                    <span class="text-sm text-indigo-100 font-medium">현재 잔액 (사용 가능)</span>
                    <span class="text-lg font-black">{{ user.current_points.toLocaleString() }}P</span>
                </div>

                <!-- 다음 티어 진행 바 -->
                <div v-if="progress">
                    <div class="flex justify-between text-xs text-indigo-200 mb-1.5">
                        <span>다음 티어: {{ progress.next_tier_icon }} {{ progress.next_tier_name }}</span>
                        <span>{{ progress.remaining.toLocaleString() }}P 남음</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2 transition-all duration-500"
                            :style="{ width: progress.percent + '%' }"></div>
                    </div>
                    <p class="text-right text-xs text-indigo-200 mt-1">{{ progress.percent }}%</p>
                </div>
                <div v-else class="text-center text-sm text-indigo-200 font-semibold py-1">
                    🏆 최고 티어 달성!
                </div>
            </div>

            <!-- 포인트 적립 방법 안내 -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-black text-gray-700 mb-3">💡 포인트 적립 방법</h3>
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <span>✍️</span><span>게시글 작성 <strong class="text-indigo-600">+50P</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <span>👍</span><span>띱(좋아요) 받음 <strong class="text-indigo-600">+5P</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <span>📩</span><span>내 글에 댓글 달림 <strong class="text-indigo-600">+3P</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <span>💬</span><span>댓글 작성(10자+) <strong class="text-indigo-600">+1P</strong></span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <span>❤️</span><span>댓글 좋아요 받음 <strong class="text-indigo-600">+5P</strong></span>
                    </div>
                </div>
            </div>

            <!-- 포인트 내역 -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-800">포인트 내역</h3>
                    <span class="text-xs text-gray-400">최신순</span>
                </div>

                <!-- 내역 없음 -->
                <div v-if="histories.data.length === 0"
                    class="py-16 text-center text-gray-400 text-sm">
                    아직 포인트 내역이 없습니다.<br/>
                    <Link :href="route('posts.create')" class="mt-3 inline-block text-indigo-500 font-semibold hover:underline">
                        첫 게시글을 작성하고 50P 받아보세요! →
                    </Link>
                </div>

                <!-- 내역 목록 -->
                <ul v-else class="divide-y divide-gray-50">
                    <li v-for="h in histories.data" :key="h.id"
                        class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                        <!-- 타입 아이콘 -->
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-base"
                            :class="h.amount > 0 ? 'bg-emerald-50' : 'bg-red-50'">
                            {{ typeIcon(h.type) }}
                        </div>
                        <!-- 설명 -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ h.type_label }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ h.created_at }}</p>
                        </div>
                        <!-- 금액 -->
                        <span class="text-sm flex-shrink-0" :class="amountClass(h.amount)">
                            {{ amountText(h.amount) }}
                        </span>
                    </li>
                </ul>

                <!-- 페이지네이션 -->
                <div v-if="histories.last_page > 1"
                    class="px-5 py-4 border-t border-gray-100 flex justify-center gap-1">
                    <Link v-for="link in histories.links" :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border"
                        :class="link.active
                            ? 'bg-indigo-600 text-white border-indigo-600'
                            : link.url
                                ? 'text-gray-600 border-gray-200 hover:bg-gray-50'
                                : 'text-gray-300 border-gray-100 pointer-events-none'"
                    />
                </div>
            </div>

        </div>
    </MainLayout>
</template>
