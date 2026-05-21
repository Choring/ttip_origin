<script setup>
import { useForm, Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    inquiry: { type: Object, required: true },
});

const page = usePage();
const success = page.props.flash?.success;

const form = useForm({
    answer: props.inquiry.answer ?? '',
});

const submit = () => form.post(route('admin.inquiries.answer', props.inquiry.id));

const destroy = () => {
    if (!confirm('이 문의를 삭제하시겠습니까?')) return;
    router.delete(route('admin.inquiries.destroy', props.inquiry.id));
};

const formatDate = (d) => d
    ? new Date(d).toLocaleString('ko-KR', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' })
    : '';
</script>

<template>
    <Head :title="`문의: ${inquiry.subject}`" />
    <AdminLayout>
        <div class="max-w-3xl space-y-6">

            <!-- 헤더 -->
            <div class="flex items-center gap-4">
                <Link :href="route('admin.inquiries.index')" class="text-gray-400 hover:text-gray-600">← 목록</Link>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-gray-900">문의 상세</h1>
                    <span class="px-2.5 py-1 rounded-full text-xs font-black"
                        :class="inquiry.status === 'pending'
                            ? 'bg-orange-100 text-orange-700'
                            : 'bg-green-100 text-green-700'">
                        {{ inquiry.status === 'pending' ? '미답변' : '답변완료' }}
                    </span>
                </div>
            </div>

            <!-- 성공 메시지 -->
            <div v-if="success" class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-bold">
                ✅ {{ success }}
            </div>

            <!-- 문의 내용 -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-wider">문의 내용</h2>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 font-semibold mb-0.5">작성자</p>
                        <p class="font-bold text-gray-900">{{ inquiry.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-semibold mb-0.5">이메일</p>
                        <p class="font-bold text-gray-900">{{ inquiry.email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-semibold mb-0.5">접수일</p>
                        <p class="text-gray-700">{{ formatDate(inquiry.created_at) }}</p>
                    </div>
                    <div v-if="inquiry.answered_at">
                        <p class="text-xs text-gray-400 font-semibold mb-0.5">답변일</p>
                        <p class="text-gray-700">{{ formatDate(inquiry.answered_at) }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-400 font-semibold mb-1.5">제목</p>
                    <p class="font-black text-gray-900 text-base">{{ inquiry.subject }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 font-semibold mb-1.5">내용</p>
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ inquiry.content }}
                    </div>
                </div>
            </div>

            <!-- 답변 작성 -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-wider">답변 작성</h2>
                <p class="text-xs text-gray-400">답변은 참고용으로 저장됩니다. 실제 이메일 발송은 별도로 진행해주세요.</p>

                <textarea v-model="form.answer" rows="6"
                    placeholder="답변 내용을 입력하세요..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                <p v-if="form.errors.answer" class="text-xs text-red-500">{{ form.errors.answer }}</p>

                <div class="flex justify-between">
                    <button type="button" @click="destroy"
                        class="px-4 py-2 bg-red-50 text-red-600 rounded-lg font-bold text-sm hover:bg-red-100 transition-colors">
                        삭제
                    </button>
                    <button @click="submit" :disabled="form.processing"
                        class="px-6 py-2 bg-orange-500 text-white rounded-lg font-black text-sm hover:bg-orange-600 disabled:opacity-50 transition-colors">
                        {{ inquiry.status === 'answered' ? '답변 수정' : '답변 저장' }}
                    </button>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
