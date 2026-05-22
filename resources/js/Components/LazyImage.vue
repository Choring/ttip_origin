<script setup>
/**
 * LazyImage — IntersectionObserver 기반 지연 로딩 이미지 컴포넌트
 *
 * - viewport 400px 전에 미리 src를 세팅하여 자연스럽게 로드
 * - 로드 완료 전: opacity-0 (부모 배경색이 skeleton 역할)
 * - 로드 완료 후: CSS @keyframes fade-in 애니메이션
 * - inheritAttrs: false → 호출부의 class/style이 <img> 에 직접 머지됨
 *   (absolute inset-0, w-full h-full 등 레이아웃 클래스를 그대로 유지)
 * - transition-all 대신 @keyframes 사용: 호출부의 transition-transform 과 충돌 없음
 */
import { ref, onMounted, onUnmounted } from 'vue';

defineOptions({ inheritAttrs: false });

const props = defineProps({
    src: String,
    alt: { type: String, default: '' },
});

const imgEl      = ref(null);
const loaded     = ref(false);
const shouldLoad = ref(false);

let observer;

onMounted(() => {
    if (!props.src) {
        shouldLoad.value = true;
        loaded.value     = true;
        return;
    }

    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                shouldLoad.value = true;
                observer?.disconnect();
            }
        },
        { rootMargin: '400px 0px' }
    );
    if (imgEl.value) observer.observe(imgEl.value);
});

onUnmounted(() => observer?.disconnect());
</script>

<template>
    <img
        ref="imgEl"
        v-bind="$attrs"
        :src="shouldLoad ? src : undefined"
        :alt="alt"
        loading="lazy"
        :class="loaded ? 'lazy-img-loaded' : 'lazy-img-loading'"
        @load="loaded = true"
        @error="loaded = true"
    />
</template>

<style>
.lazy-img-loading {
    opacity: 0;
}
.lazy-img-loaded {
    animation: lazyFadeIn 0.5s ease forwards;
}
@keyframes lazyFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
</style>
