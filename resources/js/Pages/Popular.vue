<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SummaryCard from '@/Components/SummaryCard.vue';
import { Head } from '@inertiajs/vue3';

const popularPosts = [
  {
    authorName: 'IT 위스퍼',
    authorAvatar: 'https://ui-avatars.com/api/?name=IT&background=random',
    timeAgo: '3시간 전',
    category: 'IT/개발',
    tag: 'AI',
    title: '2024년 개발자가 꼭 알아야 할 AI 도구 TOP 10',
    summary: [
      'GitHub Copilot은 코드 자동완성을 넘어 전체 함수를 생성합니다',
      'Cursor IDE는 AI 기반 코드 편집의 새로운 기준을 세웠습니다',
      'Claude와 ChatGPT를 활용한 디버깅으로 시간을 절약하세요',
    ],
    likes: 1024,
  },
  {
    authorName: '김시니어',
    authorAvatar: 'https://ui-avatars.com/api/?name=Senior&background=random',
    timeAgo: '1일 전',
    category: 'IT/개발',
    tag: '커리어',
    title: '시니어 개발자가 말하는 주니어 시절 알았더라면 좋았을 것들',
    summary: [
      '코드 리뷰는 비판이 아니라 함께 성장하는 과정입니다',
      '완벽한 코드보다 동작하는 코드를 먼저 만들고 개선하세요',
      '기술 부채를 두려워하지 말되, 관리하는 습관을 기르세요',
    ],
    likes: 892,
  },
  {
    authorName: 'FIN테크',
    authorAvatar: 'https://ui-avatars.com/api/?name=Fin&background=random',
    timeAgo: '2일 전',
    category: '생활/꿀팁',
    tag: '재테크',
    title: '사회초년생 월급 200만원으로 시작하는 재테크 로드맵',
    summary: [
      '비상금 통장에 최소 3개월 생활비를 먼저 모아두세요',
      '적금보다 CMA 통장이 유동성과 이자 모두 유리합니다',
      '소액으로 ETF 투자를 시작해 투자 감각을 익히세요',
    ],
    likes: 756,
  },
];
</script>

<template>
  <Head title="ttip - 인기글" />

  <MainLayout>
    <div class="space-y-6">
      <SummaryCard
        v-for="(post, index) in popularPosts"
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
