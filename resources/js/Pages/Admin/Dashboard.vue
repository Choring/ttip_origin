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
  Title, 
  Tooltip, 
  Legend 
} from 'chart.js';
import { Line, Bar } from 'vue-chartjs';
import { computed } from 'vue';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend
);

const props = defineProps({
    stats: Object,
    chart: Object,
});

// 차트 데이터 구성 (방문자 추이)
const visitorChartData = computed(() => ({
  labels: props.chart.labels,
  datasets: [
    {
      label: '일일 방문자 수',
      backgroundColor: '#6366f1',
      borderColor: '#6366f1',
      data: props.chart.visitors,
      tension: 0.4,
      fill: false,
    }
  ]
}));

// 차트 데이터 구성 (콘텐츠 및 가입자)
const contentChartData = computed(() => ({
  labels: props.chart.labels,
  datasets: [
    {
      label: '신규 게시글',
      backgroundColor: '#f59e0b', // amber-500
      data: props.chart.posts,
    },
    {
      label: '신규 가입자',
      backgroundColor: '#10b981', // emerald-500
      data: props.chart.users,
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
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: '#f3f4f6' }
    },
    x: {
      grid: { display: false }
    }
  }
};

const getTrend = (today, yesterday) => {
    if (!yesterday) return today > 0 ? 100 : 0;
    return Math.round(((today - yesterday) / yesterday) * 100);
};

const userTrend = computed(() => getTrend(props.stats.new_users_today, props.stats.new_users_yesterday));
const postTrend = computed(() => getTrend(props.stats.new_posts_today, props.stats.new_posts_yesterday));

</script>

<template>
    <Head title="관리자 대시보드" />

    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">대시보드 통계</h1>
            <p class="text-gray-500 mt-1 font-medium">실시간 활동 지표 및 시계열 분석 데이터를 확인합니다.</p>
        </div>
        
        <!-- 핵심 지표 카드 -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">오늘 방문자</div>
                <div class="flex items-end justify-between">
                    <div class="text-3xl font-black text-gray-800">{{ stats.today_visitors }}</div>
                    <div class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black italic">LIVE</div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">오늘 신입 회원</div>
                <div class="flex items-end justify-between">
                    <div class="text-3xl font-black text-gray-800">{{ stats.new_users_today }}</div>
                    <div :class="userTrend >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-xs font-bold leading-none mb-1">
                        {{ userTrend > 0 ? '+' : '' }}{{ userTrend }}% <span class="text-[10px] text-gray-400 ml-0.5 font-medium">vs 어제</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">오늘 게시글</div>
                <div class="flex items-end justify-between">
                    <div class="text-3xl font-black text-gray-800">{{ stats.new_posts_today }}</div>
                    <div :class="postTrend >= 0 ? 'text-emerald-500' : 'text-rose-500'" class="text-xs font-bold leading-none mb-1">
                        {{ postTrend > 0 ? '+' : '' }}{{ postTrend }}% <span class="text-[10px] text-gray-400 ml-0.5 font-medium">vs 어제</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
                <div>
                    <div class="text-gray-400 text-xs font-black uppercase tracking-widest leading-none mb-1">총 회원</div>
                    <div class="text-xl font-black text-gray-800">{{ stats.total_users }}</div>
                </div>
            </div>
        </div>

        <!-- 메인 차트 영역 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                        방문자 활동 추이 (최근 30일)
                    </h3>
                </div>
                <div class="h-[300px]">
                    <Line :data="visitorChartData" :options="chartOptions" />
                </div>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                        <span class="w-2 h-6 bg-amber-500 rounded-full"></span>
                        성장 지표 (콘텐츠 & 가입)
                    </h3>
                </div>
                <div class="h-[300px]">
                    <Bar :data="contentChartData" :options="chartOptions" />
                </div>
            </div>
        </div>

        <div class="bg-indigo-900 rounded-[32px] p-10 text-white relative overflow-hidden shadow-2xl shadow-indigo-200">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl font-black mb-2">커뮤니티 성장을 가속화하세요! 🚀</h2>
                    <p class="text-indigo-200 font-bold">더 많은 양질의 콘텐츠와 공지사항이 유저들의 활동을 끌어올립니다.</p>
                </div>
                <div class="flex gap-4">
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                        <div class="text-[10px] text-indigo-300 font-black tracking-widest uppercase mb-1">전체 게시글</div>
                        <div class="text-2xl font-black">{{ stats.total_posts }}</div>
                    </div>
                </div>
            </div>
            <!-- 배경 데코 -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -left-20 -top-20 w-60 h-60 bg-indigo-500/20 rounded-full blur-3xl"></div>
        </div>
    </AdminLayout>
</template>

