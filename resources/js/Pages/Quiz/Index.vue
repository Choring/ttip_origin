<template>
    <Head title="대구 사투리 퀴즈 | ttip" />
    <MainLayout>
        <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white pb-16">

            <!-- ── 시작 화면 ─────────────────────────────────────────── -->
            <Transition name="fade" mode="out-in">
            <div v-if="phase === 'intro'" class="flex flex-col items-center justify-center min-h-screen px-6 text-center">
                <div class="text-6xl mb-4">🗣️</div>
                <h1 class="text-2xl font-black text-gray-900 mb-2">대구 사투리 퀴즈</h1>
                <p class="text-sm text-gray-500 mb-1">경상도 사투리, 얼마나 아십니까?</p>
                <p class="text-xs text-gray-400 mb-8">총 10문제 · 3지선다</p>

                <!-- 레벨 미리보기 -->
                <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">방언 레벨</p>
                    <div class="space-y-2">
                        <div v-for="level in levels" :key="level.score" class="flex items-center gap-3">
                            <span class="text-lg w-6 text-center">{{ level.emoji }}</span>
                            <div class="flex-1">
                                <span class="text-xs font-bold text-gray-700">{{ level.title }}</span>
                                <span class="text-xs text-gray-400 ml-2">{{ level.score }}개</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    @click="startQuiz"
                    class="w-full max-w-sm bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-black text-lg py-4 rounded-2xl shadow-lg transition-all"
                >
                    퀴즈 시작하기 🚀
                </button>
            </div>
            </Transition>

            <!-- ── 퀴즈 화면 ─────────────────────────────────────────── -->
            <Transition name="fade" mode="out-in">
            <div v-if="phase === 'quiz'" class="max-w-lg mx-auto px-4 pt-8">

                <!-- 진행바 -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-indigo-600">{{ currentIndex + 1 }} / {{ questions.length }}</span>
                        <span class="text-xs text-gray-400">{{ currentIndex }}문제 완료</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div
                            class="bg-indigo-500 h-2 rounded-full transition-all duration-500"
                            :style="{ width: `${(currentIndex / questions.length) * 100}%` }"
                        />
                    </div>
                </div>

                <!-- 문제 카드 -->
                <Transition name="slide" mode="out-in">
                <div :key="currentIndex" class="bg-white rounded-3xl shadow-md border border-gray-100 p-6 mb-6">
                    <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-4">사투리가 무슨 뜻일까요?</p>
                    <p class="text-2xl font-black text-gray-900 text-center py-4 leading-relaxed">
                        "{{ currentQuestion.dialect }}"
                    </p>
                </div>
                </Transition>

                <!-- 보기 -->
                <Transition name="slide" mode="out-in">
                <div :key="'choices-' + currentIndex" class="space-y-3">
                    <button
                        v-for="(choice, idx) in currentQuestion.choices"
                        :key="idx"
                        @click="selectAnswer(choice)"
                        class="w-full bg-white border-2 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 active:scale-95 text-gray-800 font-semibold text-base py-4 px-5 rounded-2xl text-left transition-all shadow-sm"
                    >
                        <span class="inline-block w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-xs font-black text-center leading-6 mr-3">
                            {{ ['①','②','③'][idx] }}
                        </span>
                        {{ choice }}
                    </button>
                </div>
                </Transition>
            </div>
            </Transition>

            <!-- ── 결과 화면 ─────────────────────────────────────────── -->
            <Transition name="fade" mode="out-in">
            <div v-if="phase === 'result'" class="max-w-lg mx-auto px-4 pt-6 pb-16">

                <!-- 여행 스탬프 북 카드 -->
                <div class="rounded-3xl shadow-2xl overflow-hidden mb-6" style="background: #fffef5; border: 2px solid #e8e0c8;">

                    <!-- 여권 헤더 -->
                    <div class="px-5 py-4 border-b-2 border-dashed" style="border-color: #d4c9a8; background: #f5f0e0;">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold tracking-widest" style="color: #8b7355;">DAEGU TRAVEL BOOK</span>
                            <span class="text-xs font-bold" style="color: #8b7355;">대구 여행 스탬프</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-2xl font-black text-gray-900 tracking-wide">사투리 여행기</p>
                                <p class="text-xs mt-0.5" style="color: #8b7355;">경상도 방언 탐험 기록부</p>
                            </div>
                            <span class="text-4xl">🗺️</span>
                        </div>
                    </div>

                    <!-- 스탬프 영역 -->
                    <div class="px-5 py-5 border-b-2 border-dashed" style="border-color: #d4c9a8;">
                        <p class="text-xs font-bold tracking-widest mb-4 text-center" style="color: #8b7355;">— 획득한 스탬프 —</p>
                        <div class="grid grid-cols-5 gap-3">
                            <div
                                v-for="n in 10"
                                :key="n"
                                class="aspect-square rounded-xl flex items-center justify-center text-2xl border-2 transition-all"
                                :class="n <= score
                                    ? 'border-indigo-300 bg-indigo-50 shadow-sm'
                                    : 'border-dashed border-gray-200 bg-gray-50 opacity-40'"
                                :style="n <= score ? 'transform: rotate(' + ((n % 2 === 0 ? 1 : -1) * (n % 3)) + 'deg)' : ''"
                            >
                                {{ n <= score ? stampIcons[n - 1] : '　' }}
                            </div>
                        </div>
                        <p class="text-center mt-4 text-sm font-bold text-gray-600">
                            {{ score }} / 10 스탬프 획득
                        </p>
                    </div>

                    <!-- 결과 이미지 -->
                    <div class="p-4 border-b-2 border-dashed" style="border-color: #d4c9a8;">
                        <img
                            :src="`/images/quiz/quiz_lv${currentLevel.lv}.png`"
                            :alt="currentLevel.title"
                            class="w-full rounded-2xl object-cover shadow-md"
                            style="max-height: 360px;"
                        />
                    </div>

                    <!-- 등급 -->
                    <div class="text-center py-5 border-b-2 border-dashed" style="border-color: #d4c9a8; background: #f5f0e0;">
                        <p class="text-xs font-bold tracking-[0.3em] mb-2" style="color: #8b7355;">— 나의 방언 레벨 —</p>
                        <p class="text-3xl font-black text-gray-900">{{ currentLevel.emoji }} {{ currentLevel.title }}</p>
                    </div>

                    <!-- 여행 후기 멘트 -->
                    <div class="px-5 py-5 border-b-2 border-dashed" style="border-color: #d4c9a8;">
                        <p class="text-xs font-bold tracking-widest mb-4" style="color: #8b7355;">✈️ 여행 후기</p>
                        <ul class="space-y-3">
                            <li
                                v-for="(comment, idx) in currentLevel.comments"
                                :key="idx"
                                class="flex items-start gap-3 text-sm text-gray-700 leading-relaxed"
                            >
                                <span class="flex-shrink-0 mt-0.5">📌</span>
                                <span>{{ comment }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- 하단 도장 -->
                    <div class="flex justify-center items-center gap-4 py-5" style="background: #f5f0e0;">
                        <div class="text-center">
                            <div class="inline-block border-4 rounded-full px-5 py-2 rotate-[-8deg] shadow-sm" style="border-color: #c0392b; color: #c0392b;">
                                <p class="text-xs font-black tracking-widest">대구 ttip</p>
                                <p class="text-lg font-black">인증 완료</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 버튼 -->
                <div class="space-y-3">
                    <!-- 공유하기 -->
                    <button
                        @click="shareResult"
                        class="w-full active:scale-95 text-white font-black text-base py-4 rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2"
                        style="background: #FEE500; color: #3A1D1D;"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 3C6.477 3 2 6.477 2 10.5c0 2.485 1.39 4.686 3.5 6.1L4.5 21l4.2-2.8A11.5 11.5 0 0012 18c5.523 0 10-3.477 10-7.5S17.523 3 12 3z"/>
                        </svg>
                        카카오톡 공유하기
                    </button>

                    <!-- 링크 복사 -->
                    <button
                        @click="copyLink"
                        class="w-full bg-white border-2 border-gray-200 hover:border-indigo-300 active:scale-95 text-gray-600 font-bold text-base py-4 rounded-2xl transition-all flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                        </svg>
                        {{ copied ? '✅ 링크 복사됨!' : '링크 복사하기' }}
                    </button>

                    <button
                        @click="restartQuiz"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-black text-base py-4 rounded-2xl shadow-lg transition-all"
                    >
                        🔄 다시 도전하기
                    </button>
                    <Link
                        :href="route('home')"
                        class="block w-full text-center bg-white border-2 border-gray-200 hover:border-gray-300 text-gray-600 font-bold text-base py-4 rounded-2xl transition-all"
                    >
                        홈으로 돌아가기
                    </Link>
                </div>
            </div>
            </Transition>

        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    questions: Array,
});

// ── 상태 ─────────────────────────────────────────────────────────
const phase        = ref('intro');   // intro | quiz | result
const currentIndex = ref(0);
const answers      = ref([]);        // 선택한 답 배열

// 스탬프 아이콘 (대구 관련 이모지)
const stampIcons = ['🗼','🍗','🌶️','🎵','🏔️','🌸','🍺','🎭','🛤️','⭐'];

// ── 레벨 정의 ────────────────────────────────────────────────────
const levels = [
    {
        lv: 6, score: '10개', min: 10, max: 10, emoji: '🏆', title: '대구 사투리 마스터',
        comments: [
            '니는 진짜 경상도 토박이 아이가!',
            '대구 할매 할배도 인정하는 수준.',
            '"가가 가가?" 이 말 듣고 바로 이해했지?',
            '사투리 과외 한번 해보는 거 어때요?',
        ],
    },
    {
        lv: 5, score: '8~9개', min: 8, max: 9, emoji: '😎', title: '대구 토박이',
        comments: [
            '억수로 잘 하는데 한두 개가 아쉽네.',
            '대구 살다 온 거 맞지? 아니면 경상도 드라마 정주행?',
            '조금만 더 하면 진짜 토박이 소리 듣겠다.',
            '대구 치맥 먹으면서 공부한 게 티가 난다.',
        ],
    },
    {
        lv: 4, score: '6~7개', min: 6, max: 7, emoji: '🙂', title: '대구 반토박이',
        comments: [
            '경상도 친구가 한 명쯤은 있는 것 같은데?',
            '대구 여행 한 번은 갔다 온 게 분명하다.',
            '사투리를 나름 잘 안다고 생각했을 당신!',
            '이제 경상도 사투리가 슬슬 익숙해지는 단계.',
        ],
    },
    {
        lv: 3, score: '4~5개', min: 4, max: 5, emoji: '😅', title: '경상도 이주민',
        comments: [
            '드라마 속 어색한 경상도 사투리에 소름이 오소소.',
            '"와 이카노"를 "와 이카노"로 읽은 당신.',
            '단어 뜻은 이해하지만 억양 파악이 어려움.',
            '대구 맛집은 알아도 말은 아직 어색해.',
        ],
    },
    {
        lv: 2, score: '2~3개', min: 2, max: 3, emoji: '😮', title: '경상도 방문자',
        comments: [
            '"가가 가가?"가 문장이라는 것만 알았다.',
            '대구가 경상도에 있는 건 알지?',
            '경상도 사람 만나면 그냥 웃으면서 고개만 끄덕임.',
            '사투리보다 표준어가 편한 당신.',
        ],
    },
    {
        lv: 1, score: '0~1개', min: 0, max: 1, emoji: '😵', title: '서울사람',
        comments: [
            '경상도 말이 외계어로 들리는 게 분명하다.',
            '"머라카노?"를 외국어로 들었다고?',
            '대구가 어느 나라인지부터 확인해봐야 할 것 같다.',
            '사투리 공부 전에 한국 지리부터 시작하자.',
        ],
    },
];

// ── computed ─────────────────────────────────────────────────────
const currentQuestion = computed(() => props.questions[currentIndex.value]);

const score = computed(() =>
    answers.value.filter((ans, idx) => ans === props.questions[idx].answer).length
);

const currentLevel = computed(() =>
    levels.find(l => score.value >= l.min && score.value <= l.max) ?? levels[levels.length - 1]
);

// ── 메서드 ───────────────────────────────────────────────────────
const startQuiz = () => {
    phase.value = 'quiz';
};

const selectAnswer = (choice) => {
    answers.value[currentIndex.value] = choice;

    if (currentIndex.value < props.questions.length - 1) {
        currentIndex.value++;
    } else {
        phase.value = 'result';
    }
};

const restartQuiz = () => {
    router.reload();
};

const copied = ref(false);

const shareResult = () => {
    const quizUrl = window.location.href;
    const stamps = '🟣'.repeat(score.value) + '⬜'.repeat(10 - score.value);
    const text = `🗺️ 대구 사투리 퀴즈 결과\n\n${stamps}\n${score.value}/10 스탬프 획득\n\n${currentLevel.value.emoji} ${currentLevel.value.title}\n\n너도 대구 사투리 실력 테스트해봐! 👇`;

    if (window.Kakao?.Share) {
        window.Kakao.Share.sendDefault({
            objectType: 'feed',
            content: {
                title: `${currentLevel.value.emoji} ${currentLevel.value.title} — 대구 사투리 퀴즈`,
                description: `${score.value}/10 스탬프 획득! 나의 방언 레벨은?`,
                imageUrl: `https://ttip.kr/images/quiz/quiz_lv${currentLevel.value.lv}.png`,
                link: { mobileWebUrl: quizUrl, webUrl: quizUrl },
            },
            buttons: [{ title: '나도 도전하기 🚀', link: { mobileWebUrl: quizUrl, webUrl: quizUrl } }],
        });
    } else if (navigator.share) {
        navigator.share({ title: '대구 사투리 퀴즈', text, url: quizUrl });
    } else {
        copyLink();
    }
};

const copyLink = async () => {
    const quizUrl = window.location.href;
    const stamps = '🟣'.repeat(score.value) + '⬜'.repeat(10 - score.value);
    const text = `🗺️ 대구 사투리 퀴즈 결과\n\n${stamps}\n${score.value}/10 스탬프 획득\n\n${currentLevel.value.emoji} ${currentLevel.value.title}\n\n너도 대구 사투리 실력 테스트해봐! 👇\n${quizUrl}`;
    try {
        await navigator.clipboard.writeText(text);
        copied.value = true;
        setTimeout(() => copied.value = false, 2500);
    } catch {
        alert('클립보드 복사에 실패했습니다.');
    }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active { transition: all 0.3s ease; }
.slide-leave-active { transition: all 0.2s ease; }
.slide-enter-from { opacity: 0; transform: translateX(30px); }
.slide-leave-to { opacity: 0; transform: translateX(-30px); }
</style>
