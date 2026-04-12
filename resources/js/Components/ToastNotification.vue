<script setup>
import { useToast } from '@/Composables/useToast';

const { state } = useToast();
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="state.show" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] min-w-[280px]">
            <div 
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
