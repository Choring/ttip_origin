<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head } from '@inertiajs/vue3';

const samplePosts = [
  {
    authorName: '개발자김',
    authorAvatar: 'https://ui-avatars.com/api/?name=Dev&background=random',
    timeAgo: '2시간 전',
    category: 'IT/개발',
    tag: 'VSCode',
    title: 'VS Code에서 생산성을 200% 높이는 숨겨진 단축키 모음',
    summary: [
      'Ctrl+Shift+P로 모든 명령어에 빠르게 접근할 수 있습니다',
      'Alt+클릭으로 멀티 커서를 활용하면 반복 작업이 줄어듭니다',
      'Ctrl+`로 통합 터미널을 바로 열어 작업 흐름을 유지하세요',
    ],
    likes: 128,
  },
  {
    authorName: '팁마스터',
    authorAvatar: 'https://ui-avatars.com/api/?name=Tip&background=random',
    timeAgo: '5시간 전',
    category: '생활/꿀팁',
    tag: '절약',
    title: '월 50만원 절약하는 현실적인 가계부 작성법',
    summary: [
      '고정비와 변동비를 분리해서 관리하면 지출 패턴이 보입니다',
      '매일 5분 기록 습관이 한달 뒤 큰 차이를 만듭니다',
      '카테고리별 예산을 설정하고 주간 단위로 점검하세요',
    ],
    likes: 256,
  },
  {
    authorName: '디자이너박',
    authorAvatar: 'https://ui-avatars.com/api/?name=Design&background=random',
    timeAgo: '1일 전',
    category: '디자인/기획',
    tag: 'Figma',
    title: 'Figma Auto Layout 완벽 가이드 - 반응형 디자인의 핵심',
    summary: [
      'Auto Layout으로 컴포넌트를 만들면 사이즈 변경이 자유롭습니다',
      'Padding과 Gap 설정으로 일관된 간격을 유지할 수 있습니다',
      'Nested Auto Layout을 활용하면 복잡한 레이아웃도 쉽게 구현됩니다',
    ],
    likes: 89,
  },
];
</script>

<template>
  <Head title="ttip - 탐색" />

  <MainLayout>
    <div class="space-y-6">
      <SummaryCard
        v-for="(post, index) in samplePosts"
        :key="index"
        :author-name="post.authorName"
        :author-avatar="post.authorAvatar"
        :time-ago="post.timeAgo"
        :category="post.category"
        :tag="post.tag"
        :title="post.title"
        :summary="post.summary"
        :likes="post.likes"
      />
    </div>
  </MainLayout>
</template>
