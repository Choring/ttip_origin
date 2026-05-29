<template>
  <!-- iOS 홈화면 추가 안내 배너 -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="translate-y-full opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0"
    >
      <!-- iOS 안내 배너 -->
      <div
        v-if="showIosBanner"
        class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-xl safe-bottom"
        style="padding-bottom: env(safe-area-inset-bottom);"
      >
        <div class="max-w-lg mx-auto px-4 py-4">
          <!-- 닫기 버튼 -->
          <button
            @click="dismissIos"
            class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-xl leading-none"
          >
            ×
          </button>

          <div class="flex items-start gap-3 pr-6">
            <!-- ttip 아이콘 -->
            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
              <span class="text-white font-black text-lg">t</span>
            </div>

            <div class="flex-1">
              <p class="text-sm font-bold text-gray-900 mb-0.5">홈 화면에 ttip 추가하기</p>
              <p class="text-xs text-gray-500 leading-relaxed">
                앱처럼 빠르게 접속하세요!<br>
                <span class="inline-flex items-center gap-0.5">
                  아래
                  <svg class="w-3.5 h-3.5 text-blue-500 inline mx-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                    <polyline points="16 6 12 2 8 6"/>
                    <line x1="12" y1="2" x2="12" y2="15"/>
                  </svg>
                  공유 버튼 →
                  <strong class="text-gray-700">'홈 화면에 추가'</strong>
                  를 탭하세요
                </span>
              </p>
            </div>
          </div>

          <!-- 화살표 (화면 하단 가리킴) -->
          <div class="flex justify-center mt-3">
            <div class="flex items-center gap-1 text-orange-500 text-xs font-bold animate-bounce">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 18l-8-8h16l-8 8z"/>
              </svg>
              Safari 하단 공유 버튼을 탭하세요
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Android/Chrome 설치 배너 -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="translate-y-full opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0"
    >
      <div
        v-if="showAndroidBanner"
        class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-xl"
        style="padding-bottom: env(safe-area-inset-bottom);"
      >
        <div class="max-w-lg mx-auto px-4 py-4">
          <button
            @click="dismissAndroid"
            class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-xl leading-none"
          >
            ×
          </button>

          <div class="flex items-center gap-3 pr-6">
            <!-- ttip 아이콘 -->
            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
              <span class="text-white font-black text-lg">t</span>
            </div>

            <div class="flex-1">
              <p class="text-sm font-bold text-gray-900 mb-0.5">ttip 앱 설치하기</p>
              <p class="text-xs text-gray-500">홈 화면에 추가해 앱처럼 사용하세요</p>
            </div>

            <button
              @click="installAndroid"
              class="bg-orange-500 text-white text-xs font-bold px-4 py-2 rounded-full flex-shrink-0 hover:bg-orange-600 active:scale-95 transition-all"
            >
              설치
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showIosBanner  = ref(false);
const showAndroidBanner = ref(false);
let deferredPrompt = null;

const DISMISS_KEY_IOS     = 'ttip_pwa_ios_dismissed';
const DISMISS_KEY_ANDROID = 'ttip_pwa_android_dismissed';

onMounted(() => {
  // 이미 설치되어 있으면 표시 안 함
  const isStandalone =
    window.navigator.standalone === true ||
    window.matchMedia('(display-mode: standalone)').matches;

  if (isStandalone) return;

  // ── iOS Safari 감지 ──────────────────────────────────────────────────────
  const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
  const isSafari = /safari/i.test(navigator.userAgent) && !/chrome|crios|fxios/i.test(navigator.userAgent);

  if (isIos && isSafari) {
    const dismissed = localStorage.getItem(DISMISS_KEY_IOS);
    // 7일 이내 닫은 경우 재표시 안 함
    if (!dismissed || Date.now() - parseInt(dismissed) > 7 * 24 * 60 * 60 * 1000) {
      // 2초 후 표시 (페이지 로드 직후 바로 뜨면 방해됨)
      setTimeout(() => { showIosBanner.value = true; }, 2000);
    }
    return;
  }

  // ── Android/Chrome beforeinstallprompt ──────────────────────────────────
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    const dismissed = localStorage.getItem(DISMISS_KEY_ANDROID);
    if (!dismissed || Date.now() - parseInt(dismissed) > 3 * 24 * 60 * 60 * 1000) {
      setTimeout(() => { showAndroidBanner.value = true; }, 2000);
    }
  });

  // 앱 설치 완료 시 배너 숨김
  window.addEventListener('appinstalled', () => {
    showAndroidBanner.value = false;
    deferredPrompt = null;
  });
});

const dismissIos = () => {
  showIosBanner.value = false;
  localStorage.setItem(DISMISS_KEY_IOS, Date.now().toString());
};

const dismissAndroid = () => {
  showAndroidBanner.value = false;
  localStorage.setItem(DISMISS_KEY_ANDROID, Date.now().toString());
};

const installAndroid = async () => {
  if (!deferredPrompt) return;
  deferredPrompt.prompt();
  const { outcome } = await deferredPrompt.userChoice;
  if (outcome === 'accepted') {
    showAndroidBanner.value = false;
  }
  deferredPrompt = null;
};
</script>
