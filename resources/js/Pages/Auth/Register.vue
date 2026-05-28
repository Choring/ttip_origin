<script setup>
import { ref, computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';

// ── 단계 관리 ────────────────────────────────────────────────
// step 1: 약관 동의 / step 2: 정보 입력
const step = ref(1);

// ── 약관 동의 ────────────────────────────────────────────────
const agreeAll     = ref(false);
const agreeTerms   = ref(false);
const agreePrivacy = ref(false);

const toggleAll = () => {
    const val = !agreeAll.value;
    agreeAll.value     = val;
    agreeTerms.value   = val;
    agreePrivacy.value = val;
};

const syncAll = () => {
    agreeAll.value = agreeTerms.value && agreePrivacy.value;
};

const canProceed = computed(() => agreeTerms.value && agreePrivacy.value);

const goToStep2 = () => {
    if (canProceed.value) step.value = 2;
};

// ── 닉네임 중복 체크 ─────────────────────────────────────────
const nicknameStatus = ref(null); // null | 'checking' | 'available' | 'taken' | 'error'
const nicknameMessage = ref('');
let nicknameTimer = null;

const checkNickname = (val) => {
    nicknameStatus.value = null;
    if (!val || val.length < 2) return;
    clearTimeout(nicknameTimer);
    nicknameTimer = setTimeout(async () => {
        nicknameStatus.value = 'checking';
        try {
            const res = await axios.get(route('register.check-nickname'), { params: { name: val } });
            nicknameStatus.value = res.data.available ? 'available' : 'taken';
            nicknameMessage.value = res.data.message;
        } catch {
            nicknameStatus.value = 'error';
            nicknameMessage.value = '중복 확인 중 오류가 발생했습니다.';
        }
    }, 400);
};

// ── 이메일 OTP 인증 ──────────────────────────────────────────
const otpSent       = ref(false);
const otpVerified   = ref(false);
const otpCode       = ref('');
const otpSending    = ref(false);
const otpVerifying  = ref(false);
const otpError      = ref('');
const otpSuccess    = ref('');
const otpCountdown  = ref(0);
let otpTimer = null;

const startCountdown = () => {
    otpCountdown.value = 180; // 3분
    clearInterval(otpTimer);
    otpTimer = setInterval(() => {
        if (otpCountdown.value <= 0) { clearInterval(otpTimer); return; }
        otpCountdown.value--;
    }, 1000);
};

const countdownText = computed(() => {
    const m = Math.floor(otpCountdown.value / 60);
    const s = String(otpCountdown.value % 60).padStart(2, '0');
    return `${m}:${s}`;
});

const sendOtp = async () => {
    if (!form.email || otpSending.value) return;
    otpError.value   = '';
    otpSuccess.value = '';
    otpSending.value = true;
    try {
        await axios.post(route('register.send-code'), { email: form.email });
        otpSent.value    = true;
        otpVerified.value = false;
        otpCode.value    = '';
        otpSuccess.value = '인증코드가 발송되었습니다. 이메일을 확인해주세요.';
        startCountdown();
    } catch (e) {
        otpError.value = e.response?.data?.message || '발송에 실패했습니다.';
    } finally {
        otpSending.value = false;
    }
};

const verifyOtp = async () => {
    if (!otpCode.value || otpVerifying.value) return;
    otpError.value   = '';
    otpVerifying.value = true;
    try {
        await axios.post(route('register.verify-code'), { email: form.email, code: otpCode.value });
        otpVerified.value = true;
        otpSuccess.value  = '✓ 이메일 인증이 완료되었습니다.';
        clearInterval(otpTimer);
    } catch (e) {
        otpError.value = e.response?.data?.message || '인증에 실패했습니다.';
    } finally {
        otpVerifying.value = false;
    }
};

// ── 이메일 변경 시 인증 초기화 ───────────────────────────────
const onEmailChange = () => {
    otpSent.value     = false;
    otpVerified.value = false;
    otpCode.value     = '';
    otpError.value    = '';
    otpSuccess.value  = '';
    clearInterval(otpTimer);
    otpCountdown.value = 0;
};

// ── 가입 폼 ─────────────────────────────────────────────────
const form = useForm({
    name:                  '',
    email:                 '',
    password:              '',
    password_confirmation: '',
});

const canSubmit = computed(() =>
    otpVerified.value &&
    nicknameStatus.value === 'available' &&
    form.name.length >= 2 &&
    form.email &&
    form.password &&
    form.password_confirmation
);

const submit = () => {
    if (!canSubmit.value) return;
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="회원가입" />

        <!-- ── 단계 표시 ─────────────────────────────────── -->
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-all"
                    :class="step >= 1 ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400'">1</div>
                <span class="text-xs font-bold" :class="step >= 1 ? 'text-primary' : 'text-gray-400'">약관 동의</span>
            </div>
            <div class="w-8 h-px bg-gray-200"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black transition-all"
                    :class="step >= 2 ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400'">2</div>
                <span class="text-xs font-bold" :class="step >= 2 ? 'text-primary' : 'text-gray-400'">정보 입력</span>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════ -->
        <!-- STEP 1: 약관 동의                                -->
        <!-- ════════════════════════════════════════════════ -->
        <div v-if="step === 1">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">서비스 이용약관</h2>
                <p class="text-sm text-gray-500 mt-1">서비스 이용을 위해 약관에 동의해주세요.</p>
            </div>

            <!-- 전체 동의 -->
            <label class="flex items-center gap-3 p-4 bg-orange-50 border-2 border-primary rounded-2xl cursor-pointer mb-4">
                <input type="checkbox" :checked="agreeAll" @change="toggleAll" class="w-5 h-5 accent-primary rounded" />
                <span class="font-black text-gray-900">전체 동의</span>
            </label>

            <div class="space-y-3 mb-6">
                <!-- 서비스 이용약관 (필수) -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer">
                        <input type="checkbox" v-model="agreeTerms" @change="syncAll" class="w-4 h-4 accent-primary rounded" />
                        <span class="text-sm font-bold text-gray-800 flex-1">서비스 이용약관 <span class="text-primary">(필수)</span></span>
                    </label>
                    <div class="mx-4 mb-3 h-28 overflow-y-auto bg-gray-50 rounded-xl p-3 text-xs text-gray-500 leading-relaxed border border-gray-100">
                        <p class="font-bold text-gray-700 mb-1">제1조 (목적)</p>
                        <p>본 약관은 ttip(이하 "서비스")이 제공하는 대구 지역 정보 및 커뮤니티 서비스의 이용 조건 및 절차, 서비스 이용자와 서비스 운영자의 권리·의무 및 책임사항을 규정함을 목적으로 합니다.</p>
                        <p class="font-bold text-gray-700 mt-2 mb-1">제2조 (이용자의 의무)</p>
                        <p>이용자는 서비스 이용 시 타인의 권리를 침해하거나 허위 정보를 게시하는 등의 행위를 해서는 안 됩니다. 불법 콘텐츠, 스팸, 욕설 등을 게시할 경우 서비스 이용이 제한될 수 있습니다.</p>
                        <p class="font-bold text-gray-700 mt-2 mb-1">제3조 (서비스 이용)</p>
                        <p>서비스는 대구 지역 관광, 맛집, 문화행사 정보 및 커뮤니티 기능을 제공합니다. 서비스 내 게시물의 저작권은 작성자에게 있으며, 서비스는 이를 서비스 운영 목적으로 활용할 수 있습니다.</p>
                        <p class="font-bold text-gray-700 mt-2 mb-1">제4조 (면책조항)</p>
                        <p>서비스는 이용자가 게시한 정보의 정확성에 대해 보증하지 않으며, 이용자 간 분쟁에 대해 책임을 지지 않습니다.</p>
                    </div>
                </div>

                <!-- 개인정보처리방침 (필수) -->
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                    <label class="flex items-center gap-3 px-4 py-3 cursor-pointer">
                        <input type="checkbox" v-model="agreePrivacy" @change="syncAll" class="w-4 h-4 accent-primary rounded" />
                        <span class="text-sm font-bold text-gray-800 flex-1">개인정보 수집 및 이용 동의 <span class="text-primary">(필수)</span></span>
                    </label>
                    <div class="mx-4 mb-3 h-28 overflow-y-auto bg-gray-50 rounded-xl p-3 text-xs text-gray-500 leading-relaxed border border-gray-100">
                        <p class="font-bold text-gray-700 mb-1">수집 항목</p>
                        <p>이메일 주소, 닉네임, 비밀번호(암호화 저장)</p>
                        <p class="font-bold text-gray-700 mt-2 mb-1">수집 목적</p>
                        <p>회원 식별 및 서비스 제공, 이메일 인증, 공지사항 발송</p>
                        <p class="font-bold text-gray-700 mt-2 mb-1">보유 기간</p>
                        <p>회원 탈퇴 시까지 (법령에 의한 경우 해당 기간)</p>
                        <p class="font-bold text-gray-700 mt-2 mb-1">제3자 제공</p>
                        <p>원칙적으로 개인정보를 제3자에게 제공하지 않습니다. 단, 법령에 의거한 경우는 예외로 합니다.</p>
                        <p class="mt-1">자세한 내용은 <a href="/privacy" target="_blank" class="text-primary underline">개인정보처리방침</a>을 확인해주세요.</p>
                    </div>
                </div>

            </div>

            <button
                @click="goToStep2"
                :disabled="!canProceed"
                class="w-full py-3.5 rounded-full text-base font-black transition-all"
                :class="canProceed
                    ? 'bg-primary text-white hover:bg-orange-600 shadow-md'
                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
            >
                동의하고 계속하기
            </button>

            <p v-if="!canProceed" class="text-center text-xs text-gray-400 mt-2">
                필수 약관에 동의해주세요.
            </p>

            <div class="mt-5 flex items-center justify-center space-x-2 text-sm text-gray-600 font-medium pb-2">
                <span>이미 계정이 있으신가요?</span>
                <Link :href="route('login')" class="text-primary hover:text-orange-700 font-bold transition-colors">
                    로그인하기
                </Link>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════ -->
        <!-- STEP 2: 정보 입력                                -->
        <!-- ════════════════════════════════════════════════ -->
        <div v-if="step === 2">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">회원가입</h2>
                <p class="text-sm text-gray-500 mt-1">ttip의 회원이 되어 대구를 함께 탐험해요!</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <!-- 닉네임 -->
                <div>
                    <InputLabel for="name" value="닉네임" class="text-gray-700 font-bold mb-1" />
                    <div class="relative">
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all pr-10"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="최소 2글자 이상"
                            @input="checkNickname(form.name)"
                        />
                        <!-- 상태 아이콘 -->
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm mt-0.5">
                            <span v-if="nicknameStatus === 'checking'" class="text-gray-400">⏳</span>
                            <span v-else-if="nicknameStatus === 'available'" class="text-green-500">✓</span>
                            <span v-else-if="nicknameStatus === 'taken'" class="text-red-500">✗</span>
                        </span>
                    </div>
                    <p v-if="nicknameStatus === 'available'" class="mt-1 text-xs text-green-600 font-medium">{{ nicknameMessage }}</p>
                    <p v-else-if="nicknameStatus === 'taken'" class="mt-1 text-xs text-red-500 font-medium">{{ nicknameMessage }}</p>
                    <p v-else-if="form.name && form.name.length < 2" class="mt-1 text-xs text-red-500 font-medium">닉네임은 최소 2글자 이상이어야 합니다.</p>
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <!-- 이메일 + OTP 인증 -->
                <div>
                    <InputLabel for="email" value="이메일 주소" class="text-gray-700 font-bold mb-1" />
                    <div class="flex gap-2">
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                            :class="otpVerified ? 'bg-green-50 border-green-300' : ''"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="이메일 주소 입력"
                            :disabled="otpVerified"
                            @input="onEmailChange"
                        />
                        <button
                            type="button"
                            @click="sendOtp"
                            :disabled="!form.email || otpSending || otpVerified"
                            class="mt-1 flex-shrink-0 px-4 py-3 rounded-xl text-sm font-bold transition-all whitespace-nowrap"
                            :class="otpVerified
                                ? 'bg-green-100 text-green-600 cursor-not-allowed'
                                : 'bg-primary text-white hover:bg-orange-600 disabled:opacity-40 disabled:cursor-not-allowed'"
                        >
                            {{ otpVerified ? '인증완료' : otpSending ? '발송중...' : otpSent ? '재발송' : '인증코드 발송' }}
                        </button>
                    </div>
                    <InputError class="mt-1" :message="form.errors.email" />

                    <!-- OTP 입력 영역 -->
                    <div v-if="otpSent && !otpVerified" class="mt-3">
                        <div class="flex gap-2">
                            <input
                                v-model="otpCode"
                                type="text"
                                maxlength="6"
                                placeholder="인증코드 6자리"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            />
                            <button
                                type="button"
                                @click="verifyOtp"
                                :disabled="otpCode.length !== 6 || otpVerifying"
                                class="flex-shrink-0 px-4 py-3 bg-gray-800 text-white rounded-xl text-sm font-bold hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                            >
                                {{ otpVerifying ? '확인중...' : '확인' }}
                            </button>
                        </div>
                        <p v-if="otpCountdown > 0" class="mt-1 text-xs text-gray-400">
                            남은 시간: <span class="font-bold text-orange-500">{{ countdownText }}</span>
                        </p>
                        <p v-else class="mt-1 text-xs text-red-400">인증코드가 만료되었습니다. 재발송해주세요.</p>
                    </div>

                    <p v-if="otpError" class="mt-1 text-xs text-red-500 font-medium">{{ otpError }}</p>
                    <p v-if="otpSuccess && otpVerified" class="mt-1 text-xs text-green-600 font-medium">{{ otpSuccess }}</p>
                    <p v-else-if="otpSuccess && !otpVerified" class="mt-1 text-xs text-blue-500 font-medium">{{ otpSuccess }}</p>
                </div>

                <!-- 비밀번호 -->
                <div>
                    <InputLabel for="password" value="비밀번호" class="text-gray-700 font-bold mb-1" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="영문, 숫자, 특수문자 조합 8자 이상"
                    />
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <!-- 비밀번호 확인 -->
                <div>
                    <InputLabel for="password_confirmation" value="비밀번호 확인" class="text-gray-700 font-bold mb-1" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full rounded-xl border-gray-200 focus:border-primary focus:ring-primary shadow-sm px-4 py-3 text-sm transition-all"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="비밀번호를 한번 더 입력해주세요"
                    />
                    <InputError class="mt-1" :message="form.errors.password_confirmation" />
                </div>

                <!-- 가입하기 버튼 -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full justify-center flex items-center px-4 py-3.5 rounded-full text-base font-black shadow-md transition-all active:scale-[0.98]"
                        :class="canSubmit && !form.processing
                            ? 'bg-primary hover:bg-orange-600 text-white'
                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        :disabled="!canSubmit || form.processing"
                    >
                        {{ form.processing ? '가입 중...' : '회원가입 완료' }}
                    </button>
                    <p v-if="!otpVerified" class="text-center text-xs text-gray-400 mt-2">
                        이메일 인증을 먼저 완료해주세요.
                    </p>
                </div>

                <!-- 카카오 간편 가입 -->
                <div class="relative w-full my-2">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-[#F9FAFB] px-4 text-gray-500 font-bold">간편 회원가입</span>
                    </div>
                </div>
                <a :href="route('kakao.login')"
                    class="flex w-full items-center justify-center gap-3 rounded-full bg-[#FEE500] px-4 py-3.5 text-base font-bold text-[#191919] shadow-md hover:bg-[#FADA0A] transition-all active:scale-[0.98]"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3c-4.97 0-9 3.185-9 7.115 0 2.558 1.707 4.8 4.27 6.054l-.841 3.08c-.05.187.159.333.31.233l3.638-2.423c.203.028.41.042.623.042 4.97 0 9-3.186 9-7.115S16.97 3 12 3z" />
                    </svg>
                    카카오로 시작하기
                </a>

                <!-- 이전 단계로 -->
                <button type="button" @click="step = 1"
                    class="w-full text-center text-sm text-gray-400 hover:text-gray-600 font-medium transition-colors">
                    ← 이전으로 (약관 동의)
                </button>

                <div class="flex items-center justify-center space-x-2 text-sm text-gray-600 font-medium pb-2">
                    <span>이미 계정이 있으신가요?</span>
                    <Link :href="route('login')" class="text-primary hover:text-orange-700 font-bold transition-colors">
                        로그인하기
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
