<script setup>
import { ref, watch, onUnmounted } from 'vue';
import { useForm, Head, Link, usePage, router } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import AppFooter from '@/Components/AppFooter.vue';

const page = usePage();
const submitted = ref(!!page.props.flash?.success);
const countdown = ref(3);
let timer = null;

const startRedirect = () => {
    timer = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            clearInterval(timer);
            router.visit(route('home'));
        }
    }, 1000);
};

if (submitted.value) startRedirect();

watch(() => page.props.flash?.success, (val) => {
    if (val) {
        submitted.value = true;
        countdown.value = 3;
        startRedirect();
    }
});

onUnmounted(() => { if (timer) clearInterval(timer); });

const form = useForm({
    name:    '',
    email:   '',
    subject: '',
    content: '',
});

const submit = () => form.post(route('inquiry.store'));
</script>

<template>
    <Head title="문의하기 - ttip" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <AppHeader />

        <main class="w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-20 py-12 md:py-16">
            <div class="max-w-2xl mx-auto">

                <!-- ===== 접수 완료 화면 ===== -->
                <div v-if="submitted" class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">문의가 접수되었습니다!</h2>
                    <p class="text-gray-500 font-medium mb-1">입력하신 이메일로 빠르게 답변 드리겠습니다.</p>
                    <p class="text-gray-400 text-sm mb-8">감사합니다 😊</p>
                    <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 rounded-full text-sm font-bold text-gray-500">
                        <svg class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ countdown }}초 후 메인으로 이동합니다
                    </div>
                    <div class="mt-4">
                        <Link :href="route('home')" class="text-sm text-primary font-bold hover:underline">지금 바로 이동 →</Link>
                    </div>
                </div>

                <!-- ===== 문의 폼 ===== -->
                <template v-else>
                    <!-- 헤더 -->
                    <div class="mb-10">
                        <h1 class="text-3xl md:text-4xl font-black tracking-tight text-gray-900 mb-3">문의하기</h1>
                        <p class="text-gray-500 font-medium">
                            궁금한 점, 불편한 점, 제안 사항을 자유롭게 남겨주세요.<br>
                            입력하신 이메일로 빠르게 답변 드리겠습니다.
                        </p>
                    </div>

                    <!-- 폼 -->
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 space-y-5">

                            <!-- 이름 + 이메일 -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">
                                        이름 <span class="text-red-500">*</span>
                                    </label>
                                    <input v-model="form.name" type="text" placeholder="홍길동"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                                        :class="form.errors.name ? 'border-red-400' : ''" />
                                    <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">
                                        이메일 <span class="text-red-500">*</span>
                                    </label>
                                    <input v-model="form.email" type="email" placeholder="example@email.com"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                                        :class="form.errors.email ? 'border-red-400' : ''" />
                                    <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                                </div>
                            </div>

                            <!-- 제목 -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">
                                    문의 제목 <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.subject" type="text" placeholder="문의 제목을 입력해주세요"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all"
                                    :class="form.errors.subject ? 'border-red-400' : ''" />
                                <p v-if="form.errors.subject" class="text-xs text-red-500 mt-1">{{ form.errors.subject }}</p>
                            </div>

                            <!-- 내용 -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">
                                    문의 내용 <span class="text-red-500">*</span>
                                </label>
                                <textarea v-model="form.content" rows="7"
                                    placeholder="문의하실 내용을 자세히 적어주세요."
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all resize-none"
                                    :class="form.errors.content ? 'border-red-400' : ''"></textarea>
                                <div class="flex justify-between mt-1">
                                    <p v-if="form.errors.content" class="text-xs text-red-500">{{ form.errors.content }}</p>
                                    <p class="text-xs text-gray-400 ml-auto">{{ form.content.length }} / 2000</p>
                                </div>
                            </div>

                        </div>

                        <!-- 버튼 -->
                        <div class="flex justify-end gap-3">
                            <Link :href="route('home')"
                                class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 font-bold hover:bg-gray-50 transition-colors">
                                취소
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                class="px-6 py-2.5 bg-primary text-white rounded-xl font-black text-sm hover:bg-orange-600 disabled:opacity-50 transition-colors flex items-center gap-2">
                                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ form.processing ? '제출 중...' : '문의 접수하기' }}
                            </button>
                        </div>
                    </form>
                </template>

            </div>
        </main>

        <AppFooter />
    </div>
</template>
