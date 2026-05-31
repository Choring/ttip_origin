<script setup>
import { onMounted, onUnmounted, computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PortalLayout from '@/Layouts/PortalLayout.vue';
import LoginModal from '@/Components/LoginModal.vue';
import HeroBanner from '@/Components/HeroBanner.vue';
import EventsSection from '@/Components/EventsSection.vue';
import RestaurantsSection from '@/Components/RestaurantsSection.vue';
import SpotsSection from '@/Components/SpotsSection.vue';
import CommunitySection from '@/Components/CommunitySection.vue';

const showLoginModal = ref(false);

const props = defineProps({
  heroEvent:           { type: Object, default: null },
  upcomingEvents:      { type: Array,  default: () => [] },
  featuredRestaurants: { type: Array,  default: () => [] },
  featuredSpots:       { type: Array,  default: () => [] },
  recentPosts:         { type: Array,  default: () => [] },
});

// JSON-LD (WebSite + SearchAction)
const jsonLd = computed(() => {
    const base = typeof window !== 'undefined' ? window.location.origin : '';
    return {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: 'ttip',
        alternateName: '티팁',
        description: '대구 관광, 맛집, 문화행사, 지역 정보를 한곳에서. 대구의 매력을 발견하는 플랫폼',
        url: base,
        potentialAction: {
            '@type': 'SearchAction',
            target: { '@type': 'EntryPoint', urlTemplate: `${base}/community?search_keyword={search_term_string}&search_type=title` },
            'query-input': 'required name=search_term_string',
        },
    };
});

let schemaScriptEl = null;

onMounted(() => {
    schemaScriptEl = document.createElement('script');
    schemaScriptEl.type = 'application/ld+json';
    schemaScriptEl.id = 'jsonld-website';
    schemaScriptEl.textContent = JSON.stringify(jsonLd.value);
    document.head.appendChild(schemaScriptEl);
});

onUnmounted(() => {
    if (schemaScriptEl) { schemaScriptEl.remove(); schemaScriptEl = null; }
});
</script>

<template>
  <Head>
    <title>ttip - 대구를 더 깊이 즐기고 싶다면</title>
    <meta head-key="description" name="description" content="대구 관광지, 맛집, 문화행사 정보와 지역 커뮤니티. 대구의 매력을 ttip에서 발견하세요.">
    <meta head-key="og:type" property="og:type" content="website" />
    <meta head-key="og:title" property="og:title" content="ttip - 대구를 더 깊이 즐기고 싶다면" />
    <meta head-key="og:description" property="og:description" content="대구 관광지, 맛집, 문화행사 정보와 지역 커뮤니티. 대구의 매력을 ttip에서 발견하세요." />
  </Head>

  <PortalLayout>
    <div class="space-y-8">
      <!-- 오늘의 대구 히어로 배너 -->
      <HeroBanner :hero-event="heroEvent" />

      <!-- 이번 주 공연·행사 -->
      <EventsSection :events="upcomingEvents" />

      <!-- 추천 맛집 -->
      <RestaurantsSection :restaurants="featuredRestaurants" />

      <!-- 대구 관광지 -->
      <SpotsSection :spots="featuredSpots" />

      <!-- 커뮤니티 최신글 -->
      <CommunitySection :posts="recentPosts" />
    </div>
  </PortalLayout>

  <LoginModal :show="showLoginModal" @close="showLoginModal = false" />
</template>
