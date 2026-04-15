<script setup>
import { useToast } from '@/Composables/useToast';

const { state } = useToast();
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-4 opacity-0 scale-95"
        enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div v-if="state.show" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] min-w-[260px]">

            <!-- 포인트 적립 전용 토스트 -->
            <div
                v-if="state.type === 'point'"
                class="relative overflow-hidden px-6 py-4 rounded-2xl shadow-2xl border border-amber-300 flex items-center gap-3"
                style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fbbf24 100%);"
            >
                <!-- 반짝이 효과 -->
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute top-1 right-3 text-yellow-300 text-xs animate-bounce">✨</div>
                    <div class="absolute bottom-1 left-4 text-yellow-300 text-[10px] animate-pulse">⭐</div>
                </div>

                <div class="flex-shrink-0 w-10 h-10 bg-amber-400 rounded-full flex items-center justify-center shadow-inner text-xl">
                    🪙
                </div>
                <div class="relative z-10">
                    <p class="text-amber-900 font-black text-base tracking-tight leading-none">
                        {{ state.message }}
                    </p>
                    <p class="text-amber-700 text-xs font-semibold mt-0.5">포인트가 적립되었습니다</p>
                </div>
            </div>

            <!-- 일반 성공/에러 토스트 -->
            <div
                v-else
                class="px-6 py-3.5 rounded-2xl shadow-2xl border flex items-center gap-3 backdrop-blur-md"
                :class="{
                    'bg-gray-900/90 text-white border-gray-800': state.type === 'success',
                    'bg-red-600/90 text-white border-red-500': state.type === 'error',
                }"
            >
                <div class="flex-shrink-0">
                    <span v-if="state.type === 'success'" class="text-xl">✅</span>
                    <span v-else class="text-xl">⚠️</span>
                </div>
                <p class="text-sm font-bold tracking-tight">
                    {{ state.message }}
                </p>
            </div>

        </div>
    </Transition>
</template>
