<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js';
import { Line, Bar, Doughnut } from 'vue-chartjs';
import { computed, ref } from 'vue';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler,
);

const props = defineProps({
    stats:         Object,
    chart7:        Object,
    chart30:       Object,
    categoryStats: Array,
    topPosts:      Array,
    tierStats:     Array,
});

// ── 탭 상태 ──────────────────────────────────────────────────
const activeTab = ref('30'); // '7' | '30'
const currentChart = computed(() => activeTab.value === '7' ? props.chart7 : props.chart30);
const tabLabel = computed(() => activeTab.value === '7' ? '최근 7일' : '최근 30일');
const totalVisitors = computed(() => activeTab.value === '7' ? props.stats.total_visitors_7 : props.stats.total_visitors_30);

// ── 차트 데이터 ───────────────────────────────────────────────
const visitorChartData = computed(() => ({
  labels: currentChart.value.labels,
  datasets: [
    {
      label: '일일 방문자',
      backgroundColor: 'rgba(99,102,241,0.12)',
      borderColor: '#6366f1',
      pointBackgroundColor: '#6366f1',
      pointRadius: activeTab.value === '7' ? 5 : 3,
      pointHoverRadius: 7,
      data: currentChart.value.visitors,
      tension: 0.4,
      fill: true,
    }
  ]
}));

const contentChartData = computed(() => ({
  labels: currentChart.value.labels,
  datasets: [
    {
      label: '신규 게시글',
      backgroundColor: '#f59e0b',
      borderRadius: 6,
      data: currentChart.value.posts,
    },
    {
      label: '신규 가입자',
      backgroundColor: '#10b981',
      borderRadius: 6,
      data: currentChart.value.users,
    }
  ]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        font: { size: 12, weight: 'bold' },
        usePointStyle: true,
        padding: 16,
      }
    },
    tooltip: {
      backgroundColor: '#1e1b4b',
      titleFont: { size: 12, weight: 'bold' },
      bodyFont:  { size: 12 },
      padding: 12,
      cornerRadius: 10,
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: '#f3f4f6' },
      ticks: { font: { size: 11 } }
    },
    x: {
      grid: { display: false },
      ticks: { font: { size: 11 } }
    }
  }
};

// ── 추이 뱃지 ────────────────────────────────────────────────
const getTrend = (today, yesterday) => {
    if (!yesterday) return today > 0 ? 100 : 0;
    return Math.round(((today - yesterday) / yesterday) * 100);
};

const userTrend = computed(() => getTrend(props.stats.new_users_today, props.stats.new_users_yesterday));
const postTrend = computed(() => getTrend(props.stats.new_posts_today, props.stats.new_posts_yesterday));

// ── 카테고리 도넛 차트 ────────────────────────────────────────
const CATEGORY_COLORS = [
    '#6366f1', '#f59e0b', '#10b981', '#3b82f6',
    '#ec4899', '#8b5cf6', '#14b8a6', '#f97316',
];

const categoryChartData = computed(() => ({
    labels: (props.categoryStats || []).map(c => c.name),
    datasets: [{
        data: (props.categoryStats || []).map(c => c.count),
        backgroundColor: CATEGORY_COLORS,
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 6,
    }],
}));

// ── 티어 도넛 차트 ────────────────────────────────────────────
const TIER_COLORS = ['#fbbf24', '#a78bfa', '#34d399', '#60a5fa', '#f87171', '#94a3b8'];

const tierChartData = computed(() => ({
    labels: (props.tierStats || []).map(t => `${t.icon} ${t.name}`),
    datasets: [{
        data: (props.tierStats || []).map(t => t.count),
        backgroundColor: TIER_COLORS,
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 6,
    }],
}));

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '65%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                font: { size: 11, weight: 'bold' },
                usePointStyle: true,
                padding: 12,
            },
        },
        tooltip: {
            backgroundColor: '#1e1b4b',
            titleFont: { size: 12, weight: 'bold' },
            bodyFont: { size: 12 },
            padding: 12,
            cornerRadius: 10,
            callbacks: {
                label: (ctx) => ` ${ctx.label}: ${ctx.parsed}개 (${Math.round(ctx.parsed / ctx.dataset.data.reduce((a, b) => a + b, 0) * 100)}%)`,
            },
        },
    },
};

// ── 숫자 포맷 ────────────────────────────────────────────────
const formatNum = (n) => n?.toLocaleString('ko-KR') ?? '0';
</script>

<template>
    <Head title="관리자 대시보드" />

    <AdminLayout>
        <!-- 헤더 -->
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">대시보드 통계</h1>
                <p class="text-gray-500 mt-1 font-medium">실시간 활동 지표 및 시계열 분석 데이터</p>
            </div>
            <!-- 기간 탭 전환 -->
            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-2xl">
                <button
                    @click="activeTab = '7'"
                    :class="activeTab === '7'
                        ? 'bg-white text-indigo-600 shadow-sm font-black'
                        : 'text-gray-500 font-bold hover:text-gray-700'"
                    class="px-5 py-2 rounded-xl text-sm transition-all"
                >
                    최근 7일
                </button>
                <button
                    @click="activeTab = '30'"
                    :class="activeTab === '30'
                        ? 'bg-white text-indigo-600 shadow-sm font-black'
                        : 'text-gray-500 font-bold hover:text-gray-700'"
                    class="px-5 py-2 rounded-xl text-sm transition-all"
                >
                    최근 30일
                </button>
            </div>
        </div>
        
        <!-- 핵심 지표 카드 -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <!-- 오늘 방문자 -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-50 rounded-full"></div>
                <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2 relative z-10">오늘 방문자</div>
                <div class="flex items-end justify-between relative z-10">
                    <div class="text-3xl font-black text-gray-800">{{ formatNum(stats.today_visitors) }}</div>
                    <div class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black animate-pulse">LIVE</div>
                </div>
                <div class="mt-3 pt-2 border-t border-gray-50 text-xs text-gray-400 font-medium relative z-10">
                    <span class="font-black text-gray-600">{{ formatNum(totalVisitors) }}</span>명 {{ tabLabel }}
                </div>
            </div>

            <!-- 신규 회원 -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-50 rounded-full"></div>
                <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2 relative z-10">오늘 신규 회원</div>
                <div class="flex items-end justify-between relative z-10">
                    <div class="text-3xl font-black text-gray-800">{{ formatNum(stats.new_users_today) }}</div>
                    <div
                        :class="userTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-rose-500 bg-rose-50'"
                        class="px-2 py-1 rounded-lg text-xs font-black"
                    >
                        {{ userTrend > 0 ? '+' : '' }}{{ userTrend }}%
                    </div>
                </div>
                <div class="mt-3 pt-2 border-t border-gray-50 text-xs text-gray-400 font-medium relative z-10">vs 어제 {{ formatNum(stats.new_users_yesterday) }}명</div>
            </div>

            <!-- 오늘 게시글 -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-50 rounded-full"></div>
                <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2 relative z-10">오늘 게시글</div>
                <div class="flex items-end justify-between relative z-10">
                    <div class="text-3xl font-black text-gray-800">{{ formatNum(stats.new_posts_today) }}</div>
                    <div
                        :class="postTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-rose-500 bg-rose-50'"
                        class="px-2 py-1 rounded-lg text-xs font-black"
                    >
                        {{ postTrend > 0 ? '+' : '' }}{{ postTrend }}%
                    </div>
                </div>
                <div class="mt-3 pt-2 border-t border-gray-50 text-xs text-gray-400 font-medium relative z-10">vs 어제 {{ formatNum(stats.new_posts_yesterday) }}건</div>
            </div>

            <!-- 총 회원 / 총 게시글 -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-violet-50 rounded-full"></div>
                <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2 relative z-10">누적 데이터</div>
                <div class="flex flex-col gap-2 relative z-10">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400 font-bold">총 회원</span>
                        <span class="text-xl font-black text-gray-800">{{ formatNum(stats.total_users) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400 font-bold">총 게시글</span>
                        <span class="text-xl font-black text-gray-800">{{ formatNum(stats.total_posts) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 차트 영역 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- 방문자 추이 차트 -->
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                        방문자 활동 추이
                    </h3>
                    <span class="text-xs font-black text-indigo-500 bg-indigo-50 px-3 py-1 rounded-full">
                        {{ tabLabel }}
                    </span>
                </div>
                <div class="h-[280px]">
                    <Line :data="visitorChartData" :options="chartOptions" :key="'visitor-' + activeTab" />
                </div>
                <!-- 합계 요약 -->
                <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-3">
                    <div class="bg-indigo-50 rounded-2xl px-4 py-3">
                        <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">기간 총 방문자</div>
                        <div class="text-xl font-black text-indigo-700">{{ formatNum(totalVisitors) }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl px-4 py-3">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">일 평균</div>
                        <div class="text-xl font-black text-gray-700">
                            {{ activeTab === '7'
                                ? formatNum(Math.round(stats.total_visitors_7 / 7))
                                : formatNum(Math.round(stats.total_visitors_30 / 30)) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- 콘텐츠 & 가입자 차트 -->
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-amber-500 rounded-full"></span>
                        성장 지표 (콘텐츠 &amp; 가입)
                    </h3>
                    <span class="text-xs font-black text-amber-600 bg-amber-50 px-3 py-1 rounded-full">
                        {{ tabLabel }}
                    </span>
                </div>
                <div class="h-[280px]">
                    <Bar :data="contentChartData" :options="chartOptions" :key="'content-' + activeTab" />
                </div>
                <!-- 합계 요약 -->
                <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-3">
                    <div class="bg-amber-50 rounded-2xl px-4 py-3">
                        <div class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-1">기간 게시글 합계</div>
                        <div class="text-xl font-black text-amber-700">
                            {{ formatNum(currentChart.posts?.reduce((a, b) => a + b, 0)) }}
                        </div>
                    </div>
                    <div class="bg-emerald-50 rounded-2xl px-4 py-3">
                        <div class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">기간 신규 가입</div>
                        <div class="text-xl font-black text-emerald-700">
                            {{ formatNum(currentChart.users?.reduce((a, b) => a + b, 0)) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 카테고리 분포 & 회원 등급 분포 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- 카테고리별 게시글 분포 -->
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-400 rounded-full"></span>
                        카테고리별 게시글 분포
                    </h3>
                    <span class="text-xs font-black text-gray-400 bg-gray-50 px-3 py-1 rounded-full">
                        전체 누적
                    </span>
                </div>
                <div v-if="categoryStats && categoryStats.length > 0" class="h-[260px]">
                    <Doughnut :data="categoryChartData" :options="doughnutOptions" />
                </div>
                <div v-else class="h-[260px] flex items-center justify-center text-gray-300 text-sm font-bold">
                    데이터 없음
                </div>
                <!-- 카테고리별 수치 나열 -->
                <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-2">
                    <div
                        v-for="(cat, idx) in (categoryStats || [])"
                        :key="cat.name"
                        class="flex items-center gap-2"
                    >
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ backgroundColor: CATEGORY_COLORS[idx % CATEGORY_COLORS.length] }"></span>
                        <span class="text-xs text-gray-500 font-bold truncate">{{ cat.name }}</span>
                        <span class="text-xs font-black text-gray-700 ml-auto">{{ formatNum(cat.count) }}</span>
                    </div>
                </div>
            </div>

            <!-- 회원 등급 분포 -->
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-amber-400 rounded-full"></span>
                        회원 등급 분포
                    </h3>
                    <span class="text-xs font-black text-gray-400 bg-gray-50 px-3 py-1 rounded-full">
                        전체 회원 {{ formatNum(stats.total_users) }}명
                    </span>
                </div>
                <div v-if="tierStats && tierStats.length > 0" class="h-[260px]">
                    <Doughnut :data="tierChartData" :options="doughnutOptions" />
                </div>
                <div v-else class="h-[260px] flex items-center justify-center text-gray-300 text-sm font-bold">
                    데이터 없음
                </div>
                <!-- 등급별 수치 나열 -->
                <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-2">
                    <div
                        v-for="(tier, idx) in (tierStats || [])"
                        :key="tier.name"
                        class="flex items-center gap-2"
                    >
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ backgroundColor: TIER_COLORS[idx % TIER_COLORS.length] }"></span>
                        <span class="text-xs text-gray-500 font-bold truncate">{{ tier.icon }} {{ tier.name }}</span>
                        <span class="text-xs font-black text-gray-700 ml-auto">{{ formatNum(tier.count) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 인기 게시글 TOP 5 -->
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-base font-black text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-6 bg-rose-400 rounded-full"></span>
                    🔥 인기 게시글 TOP 5
                </h3>
                <span class="text-xs font-black text-rose-500 bg-rose-50 px-3 py-1 rounded-full">조회수 기준</span>
            </div>
            <div v-if="topPosts && topPosts.length > 0" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-gray-100">
                            <th class="pb-3 text-[11px] font-black text-gray-400 uppercase tracking-wider w-8">#</th>
                            <th class="pb-3 text-[11px] font-black text-gray-400 uppercase tracking-wider">제목</th>
                            <th class="pb-3 text-[11px] font-black text-gray-400 uppercase tracking-wider w-20 text-center">카테고리</th>
                            <th class="pb-3 text-[11px] font-black text-gray-400 uppercase tracking-wider w-20 text-right">조회수</th>
                            <th class="pb-3 text-[11px] font-black text-gray-400 uppercase tracking-wider w-20 text-right">좋아요</th>
                            <th class="pb-3 text-[11px] font-black text-gray-400 uppercase tracking-wider w-16 text-right">작성일</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr
                            v-for="(post, idx) in topPosts"
                            :key="post.id"
                            class="hover:bg-gray-50 transition-colors group"
                        >
                            <td class="py-3.5 pr-3">
                                <span
                                    class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] font-black"
                                    :class="idx === 0 ? 'bg-amber-100 text-amber-600' : idx === 1 ? 'bg-gray-100 text-gray-500' : idx === 2 ? 'bg-orange-50 text-orange-400' : 'bg-gray-50 text-gray-400'"
                                >{{ idx + 1 }}</span>
                            </td>
                            <td class="py-3.5 pr-4">
                                <a
                                    :href="route('posts.show', post.id)"
                                    target="_blank"
                                    class="font-bold text-gray-800 hover:text-indigo-600 transition-colors line-clamp-1 group-hover:underline"
                                >{{ post.title }}</a>
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="text-[11px] font-black px-2 py-1 bg-indigo-50 text-indigo-500 rounded-lg">{{ post.category }}</span>
                            </td>
                            <td class="py-3.5 text-right">
                                <span class="font-black text-gray-700">{{ formatNum(post.views) }}</span>
                            </td>
                            <td class="py-3.5 text-right">
                                <span class="font-black text-rose-500">♥ {{ formatNum(post.likes) }}</span>
                            </td>
                            <td class="py-3.5 text-right text-gray-400 font-medium text-xs">{{ post.createdAt }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="text-center py-10 text-gray-300 text-sm font-bold">데이터 없음</div>
        </div>

        <!-- 하단 배너 -->
        <div class="bg-indigo-900 rounded-[32px] p-10 text-white relative overflow-hidden shadow-2xl shadow-indigo-200">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-black mb-2">커뮤니티 성장을 가속화하세요! 🚀</h2>
                    <p class="text-indigo-200 font-bold">더 많은 양질의 콘텐츠와 공지사항이 유저들의 활동을 끌어올립니다.</p>
                </div>
                <div class="flex gap-4">
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                        <div class="text-[10px] text-indigo-300 font-black tracking-widest uppercase mb-1">전체 게시글</div>
                        <div class="text-2xl font-black">{{ formatNum(stats.total_posts) }}</div>
                    </div>
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                        <div class="text-[10px] text-indigo-300 font-black tracking-widest uppercase mb-1">전체 회원</div>
                        <div class="text-2xl font-black">{{ formatNum(stats.total_users) }}</div>
                    </div>
                </div>
            </div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -top-20 w-60 h-60 bg-indigo-500/20 rounded-full blur-3xl"></div>
        </div>
    </AdminLayout>
</template>
