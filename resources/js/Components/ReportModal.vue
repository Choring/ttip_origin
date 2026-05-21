<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    reportableType: {
        type: String, // 'post' | 'comment'
        required: true,
    },
    reportableId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['close']);

const reason      = ref('');
const description = ref('');
const isSubmitting = ref(false);
const errorMsg    = ref('');
const success     = ref(false);

const reasons = [
    { value: 'spam',    label: '스팸 / 광고' },
    { value: 'abuse',   label: '욕설 / 비방' },
    { value: 'obscene', label: '음란물' },
    { value: 'illegal', label: '불법 정보' },
    { value: 'other',   label: '기타' },
];

const submit = async () => {
    if (!reason.value || isSubmitting.value) return;
    errorMsg.value  = '';
    isSubmitting.value = true;
    try {
        await axios.post(route('reports.store'), {
            reportable_type: props.reportableType,
            reportable_id:   props.reportableId,
            reason:          reason.value,
            description:     description.value || null,
        });
        success.value = true;
    } catch (e) {
        errorMsg.value = e.response?.data?.message || '신고 접수 중 오류가 발생했습니다.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <!-- 배경 오버레이 -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
        @click.self="emit('close')">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            <!-- 헤더 -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-800">
                    🚨 {{ reportableType === 'post' ? '게시글' : '댓글' }} 신고하기
                </h3>
                <button @click="emit('close')"
                    class="text-gray-400 hover:text-gray-600 transition text-xl font-light leading-none">✕</button>
            </div>

            <!-- 완료 상태 -->
            <div v-if="success" class="px-6 py-10 text-center">
                <div class="text-5xl mb-4">✅</div>
                <p class="text-gray-800 font-bold text-base mb-1">신고가 접수되었습니다</p>
                <p class="text-gray-500 text-sm">검토 후 적절한 조치를 취하겠습니다.</p>
                <button @click="emit('close')"
                    class="mt-6 px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                    닫기
                </button>
            </div>

            <!-- 신고 폼 -->
            <form v-else @submit.prevent="submit" class="px-6 py-5 space-y-4">
                <!-- 사유 선택 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">신고 사유 <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label v-for="r in reasons" :key="r.value"
                            class="flex items-center gap-2 border rounded-lg px-3 py-2 cursor-pointer transition text-sm font-medium"
                            :class="reason === r.value
                                ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                            <input type="radio" v-model="reason" :value="r.value" class="hidden" />
                            <span class="w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center"
                                :class="reason === r.value ? 'border-indigo-500' : 'border-gray-300'">
                                <span v-if="reason === r.value" class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            </span>
                            {{ r.label }}
                        </label>
                    </div>
                </div>

                <!-- 추가 설명 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">추가 설명 <span class="text-gray-400 font-normal">(선택)</span></label>
                    <textarea v-model="description" rows="3" maxlength="500"
                        placeholder="신고 내용을 간략히 설명해주세요..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                    <p class="text-right text-xs text-gray-400 mt-0.5">{{ description.length }}/500</p>
                </div>

                <!-- 에러 메시지 -->
                <p v-if="errorMsg" class="text-sm text-red-500 font-medium">{{ errorMsg }}</p>

                <!-- 버튼 -->
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="emit('close')"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition">
                        취소
                    </button>
                    <button type="submit"
                        :disabled="!reason || isSubmitting"
                        class="px-5 py-2 text-sm font-semibold bg-red-500 hover:bg-red-600 text-white rounded-lg transition disabled:opacity-50">
                        {{ isSubmitting ? '접수 중...' : '신고 접수' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>
