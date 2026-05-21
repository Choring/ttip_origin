<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    tier: {
        type: Object, // { name, icon }
        required: true,
    },
});

const emit = defineEmits(['close']);

const visible = ref(false);

onMounted(() => {
    // 약간의 딜레이 후 등장 (페이지 전환 애니메이션과 겹치지 않도록)
    setTimeout(() => { visible.value = true; }, 300);
    // 5초 후 자동 닫힘
    setTimeout(() => { emit('close'); }, 5000);
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-500 ease-out"
        enter-from-class="opacity-0 scale-75"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-300 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-75"
    >
        <div v-if="visible"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            @click.self="emit('close')">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm text-center overflow-hidden">

                <!-- 상단 그라디언트 배너 -->
                <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-400 pt-10 pb-8 px-6 relative overflow-hidden">
                    <!-- 별 파티클 배경 -->
                    <div class="absolute inset-0 overflow-hidden pointer-events-none select-none">
                        <span v-for="i in 12" :key="i"
                            class="absolute text-white/30 text-2xl animate-bounce"
                            :style="{
                                left: (i * 8.3) + '%',
                                top: (Math.sin(i) * 40 + 30) + '%',
                                animationDelay: (i * 0.15) + 's',
                                animationDuration: (1.2 + i * 0.1) + 's',
                            }">✦</span>
                    </div>
                    <!-- 티어 아이콘 -->
                    <div class="relative z-10">
                        <div class="text-7xl mb-3 drop-shadow-lg">{{ tier.icon }}</div>
                        <p class="text-white/80 text-sm font-semibold tracking-widest uppercase">Tier Up!</p>
                        <h2 class="text-white text-3xl font-black mt-1">{{ tier.name }}</h2>
                    </div>
                </div>

                <!-- 하단 내용 -->
                <div class="px-8 py-7">
                    <p class="text-gray-700 font-bold text-base leading-relaxed">
                        축하합니다! 🎉<br/>
                        <span class="text-indigo-600">{{ tier.name }}</span> 등급으로 승급했습니다!
                    </p>
                    <p class="text-gray-400 text-sm mt-2">
                        꾸준한 활동으로 획득한 훈장이에요.
                    </p>

                    <button @click="emit('close')"
                        class="mt-6 w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-black rounded-xl shadow-md transition-all">
                        감사합니다! 🙌
                    </button>
                </div>

            </div>
        </div>
    </Transition>
</template>
