<script setup>
/**
 * LazyImage — IntersectionObserver 기반 지연 로딩 이미지 컴포넌트
 *
 * - viewport 400px 전에 미리 src를 세팅하여 자연스럽게 로드
 * - 로드 완료 전: opacity-0 (부모 배경색이 skeleton 역할)
 * - 로드 완료 후: opacity-100 으로 fade-in
 * - inheritAttrs: false → 호출부의 class/style이 <img> 에 직접 머지됨
 *   (absolute inset-0, w-full h-full 등 레이아웃 클래스를 그대로 유지)
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
        // src 없으면 즉시 표시 (렌더링은 하되 로드 완료 처리)
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
    <!--
        v-bind="$attrs" : 호출부 class/style 그대로 전달
        :class          : 로드 전후 opacity 제어 (호출부 class와 자동 머지됨)
        transition-all  : opacity 및 transform(hover scale) 모두 커버
    -->
    <img
        ref="imgEl"
        v-bind="$attrs"
        :src="shouldLoad ? src : undefined"
        :alt="alt"
        loading="lazy"
        :class="[
            'transition-all duration-500',
            loaded ? 'opacity-100' : 'opacity-0',
        ]"
        @load="loaded = true"
        @error="loaded = true"
    />
</template>
