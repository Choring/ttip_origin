<script setup>
import { ref, computed } from 'vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
    postId:  { type: Number, required: true },
    title:   { type: String, required: true },
    summary: { type: [String, Array], default: '' },
    imageUrl:{ type: String, default: null },
});

const { showToast } = useToast();
const copied = ref(false);

const pageUrl = computed(() => {
    try { return route('posts.show', props.postId); }
    catch { return window.location.href; }
});

const summaryText = computed(() =>
    Array.isArray(props.summary) ? props.summary.join(' ') : (props.summary || '')
);

// ── 링크 복사 ──────────────────────────────────────────────
const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(pageUrl.value);
        copied.value = true;
        showToast('링크가 클립보드에 복사되었습니다! 🔗', 'success');
        setTimeout(() => { copied.value = false; }, 2500);
    } catch {
        showToast('복사에 실패했습니다. 주소를 직접 복사해 주세요.', 'error');
    }
};

// ── X(트위터) 공유 ─────────────────────────────────────────
const shareTwitter = () => {
    const text = encodeURIComponent(`${props.title}\n`);
    const url  = encodeURIComponent(pageUrl.value);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
};

// ── 카카오톡 공유 ─────────────────────────────────────────
const shareKakao = () => {
    if (!window.Kakao?.isInitialized()) {
        // Kakao SDK 없으면 kakaolink로 fallback
        const encodedUrl = encodeURIComponent(pageUrl.value);
        window.open(`https://sharer.kakao.com/talk/friends/picker/link?app_key=KAKAO_APP_KEY&validation_action=custom&validation_params={"url":"${encodedUrl}"}`, '_blank');
        return;
    }
    window.Kakao.Link.sendDefault({
        objectType: 'feed',
        content: {
            title: props.title,
            description: summaryText.value.substring(0, 100),
            imageUrl: props.imageUrl || 'https://ttip.kr/og-default.png',
            link: { mobileWebUrl: pageUrl.value, webUrl: pageUrl.value },
        },
        buttons: [{ title: '자세히 보기', link: { mobileWebUrl: pageUrl.value, webUrl: pageUrl.value } }],
    });
};

// ── Web Share API (모바일 기본 공유) ──────────────────────
const nativeShare = async () => {
    const shareData = {
        title: props.title,
        text: summaryText.value,
        url: pageUrl.value,
    };
    try {
        if (navigator.share && navigator.canShare?.(shareData)) {
            await navigator.share(shareData);
        } else {
            await copyLink();
        }
    } catch (err) {
        if (err.name !== 'AbortError') await copyLink();
    }
};
</script>

<template>
    <div class="flex items-center gap-2 flex-wrap">

        <!-- 링크 복사 버튼 -->
        <button
            @click="copyLink"
            :class="copied ? 'bg-green-50 border-green-300 text-green-600' : 'bg-white border-gray-200 text-gray-600 hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600'"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border text-xs font-bold transition-all shadow-sm"
            :title="copied ? '복사됨!' : '링크 복사'"
        >
            <svg v-if="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            {{ copied ? '복사됨!' : '링크 복사' }}
        </button>

        <!-- X(트위터) 공유 -->
        <button
            @click="shareTwitter"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-bold hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all shadow-sm"
            title="X(트위터)에 공유"
        >
            <!-- X logo -->
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.254 5.622 5.91-5.622Zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            X 공유
        </button>

        <!-- 카카오톡 공유 -->
        <button
            @click="shareKakao"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-yellow-300 bg-yellow-400 text-yellow-900 text-xs font-bold hover:bg-yellow-500 transition-all shadow-sm"
            title="카카오톡으로 공유"
        >
            <!-- Kakao bubble icon -->
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 3C6.477 3 2 6.582 2 11.018c0 2.861 1.703 5.38 4.271 6.914-.188.668-.684 2.415-.782 2.788-.118.464.17.458.358.334.148-.099 2.348-1.592 3.3-2.238.593.085 1.205.13 1.853.13 5.523 0 10-3.582 10-8.018C22 6.582 17.523 3 12 3z"/>
            </svg>
            카카오 공유
        </button>

        <!-- 모바일 네이티브 공유 (모바일에서만 표시) -->
        <button
            @click="nativeShare"
            class="md:hidden flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 bg-white text-gray-600 text-xs font-bold hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all shadow-sm"
            title="공유하기"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
            </svg>
            더 공유
        </button>
    </div>
</template>
