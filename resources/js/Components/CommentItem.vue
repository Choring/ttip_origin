<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';
import ReportModal from '@/Components/ReportModal.vue';
import axios from 'axios';

const props = defineProps({
    comment: Object,
    postId: Number,
    depth: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits([
    'open-login-modal',
    'comment-added',
    'comment-updated',
    'comment-deleted',
    'comment-liked',
]);

const page = usePage();
const user = page.props.auth.user;
const { showPointToast } = useToast();

const showReplyForm = ref(false);
const showEditForm  = ref(false);

const replyContent = ref('');
const editContent  = ref(props.comment.content);

const isReplying  = ref(false);
const isEditing   = ref(false);
const isDeleting  = ref(false);
const isLiking    = ref(false);

const showReportModal = ref(false);

// ── 답글 작성 ────────────────────────────────────────────────────
const submitReply = async () => {
    if (!replyContent.value.trim() || isReplying.value) return;
    isReplying.value = true;
    try {
        const res = await axios.post(route('comments.store', { post: props.postId }), {
            content: replyContent.value,
            parent_id: props.comment.id,
        });
        emit('comment-added', res.data.comment);
        if (res.data.point_gain) showPointToast(res.data.point_gain);
        replyContent.value = '';
        showReplyForm.value = false;
    } catch (e) {
        console.error(e);
    } finally {
        isReplying.value = false;
    }
};

// ── 댓글 수정 ────────────────────────────────────────────────────
const submitEdit = async () => {
    if (!editContent.value.trim() || isEditing.value) return;
    isEditing.value = true;
    try {
        await axios.put(route('comments.update', { comment: props.comment.id }), {
            content: editContent.value,
        });
        emit('comment-updated', { id: props.comment.id, content: editContent.value });
        showEditForm.value = false;
    } catch (e) {
        console.error(e);
    } finally {
        isEditing.value = false;
    }
};

// ── 댓글 삭제 ────────────────────────────────────────────────────
const deleteComment = async () => {
    if (!confirm('정말로 이 댓글을 삭제하시겠습니까? (하위 대댓글도 모두 삭제됩니다)')) return;
    isDeleting.value = true;
    try {
        const res = await axios.delete(route('comments.destroy', { comment: props.comment.id }));
        emit('comment-deleted', { id: props.comment.id, replyIds: res.data.reply_ids || [] });
    } catch (e) {
        console.error(e);
    } finally {
        isDeleting.value = false;
    }
};

// ── 댓글 좋아요 ──────────────────────────────────────────────────
const toggleLike = async () => {
    if (!user) {
        emit('open-login-modal', 'like');
        return;
    }
    if (isLiking.value) return;
    isLiking.value = true;
    try {
        const res = await axios.post(route('comments.like', { comment: props.comment.id }));
        emit('comment-liked', {
            id: props.comment.id,
            liked: res.data.liked,
            likes_count: res.data.likes_count,
        });
    } catch (e) {
        console.error(e);
    } finally {
        isLiking.value = false;
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};
</script>

<template>
    <div class="mb-4">
        <div class="flex space-x-3">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full object-cover"
                    :src="'https://ui-avatars.com/api/?name=' + encodeURI(comment.user?.name || '?') + '&background=random'"
                    alt="" />
            </div>

            <div class="flex-grow max-w-full">
                <div class="bg-gray-50 rounded-2xl px-4 py-3 shadow-sm border border-gray-100">
                    <div class="mb-1">
                        <div class="flex items-center space-x-1 font-bold text-sm text-gray-900">
                            <span v-if="comment.user?.tier?.icon_url" :title="comment.user.tier.name" class="text-xs">{{ comment.user.tier.icon_url }}</span>
                            <span>{{ comment.user?.name || '알 수 없음' }}</span>
                        </div>
                        <span class="text-[11px] text-gray-400">{{ formatDate(comment.created_at) }}</span>
                    </div>

                    <!-- 내용 -->
                    <div v-if="!showEditForm" class="text-gray-800 text-sm whitespace-pre-wrap leading-relaxed break-words">
                        {{ comment.content }}
                    </div>

                    <!-- 수정 폼 -->
                    <form v-else @submit.prevent="submitEdit" class="mt-2 text-right">
                        <textarea v-model="editContent" rows="2"
                            class="w-full p-2 text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required></textarea>
                        <div class="mt-2 space-x-2">
                            <button type="button" @click="showEditForm = false"
                                class="text-xs font-semibold text-gray-500 hover:text-gray-700 transition">취소</button>
                            <button type="submit"
                                class="text-xs font-semibold bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                                :disabled="isEditing">{{ isEditing ? '저장 중...' : '수정 완료' }}</button>
                        </div>
                    </form>
                </div>

                <!-- Actions -->
                <div class="mt-1 ml-2 flex items-center space-x-4 text-xs text-gray-400 font-semibold">
                    <button @click="toggleLike"
                        class="flex items-center space-x-1 transition"
                        :class="comment.isLiked ? 'text-indigo-600' : 'hover:text-indigo-600'"
                        :disabled="isLiking">
                        <span>좋아요</span>
                        <span v-if="comment.likes_count > 0">{{ comment.likes_count }}</span>
                    </button>
                    <button v-if="user" @click="showReplyForm = !showReplyForm"
                        class="hover:text-indigo-600 transition">답글 달기</button>
                    <button v-if="user && user.id === comment.user_id"
                        @click="showEditForm = !showEditForm; editContent = comment.content"
                        class="hover:text-amber-600 transition">수정</button>
                    <button v-if="user && user.id === comment.user_id"
                        @click="deleteComment"
                        class="hover:text-red-600 transition disabled:opacity-40"
                        :disabled="isDeleting">{{ isDeleting ? '삭제 중...' : '삭제' }}</button>
                    <!-- 신고 버튼: 본인 댓글 제외 -->
                    <button v-if="user && user.id !== comment.user_id"
                        @click="showReportModal = true"
                        class="hover:text-red-400 transition ml-auto">신고</button>
                </div>

                <!-- 답글 폼 -->
                <form v-if="showReplyForm && user" @submit.prevent="submitReply"
                    class="mt-3 relative flex items-center">
                    <img class="absolute left-2 h-7 w-7 rounded-full object-cover"
                        :src="'https://ui-avatars.com/api/?name=' + encodeURI(user.name) + '&background=random'"
                        alt="" />
                    <input v-model="replyContent" type="text" placeholder="답글을 입력하세요..."
                        class="w-full pl-11 pr-20 py-2.5 border border-gray-200 rounded-full text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-inner"
                        required />
                    <button type="submit"
                        class="absolute right-1 bottom-1 top-1 px-4 bg-indigo-600 hover:bg-indigo-700 transition text-white text-xs font-semibold rounded-full disabled:opacity-50"
                        :disabled="isReplying">{{ isReplying ? '...' : '작성' }}</button>
                </form>
            </div>
        </div>

        <!-- 댓글 신고 모달 -->
        <ReportModal
            v-if="showReportModal"
            reportable-type="comment"
            :reportable-id="comment.id"
            @close="showReportModal = false"
        />

        <!-- 대댓글 -->
        <div v-if="comment.children && comment.children.length > 0"
            :class="['mt-3', depth === 0 ? 'ml-12 border-l-2 border-indigo-100 pl-4 py-1' : '']">
            <CommentItem
                v-for="child in comment.children"
                :key="child.id"
                :comment="child"
                :postId="postId"
                :depth="depth + 1"
                @open-login-modal="(type) => emit('open-login-modal', type)"
                @comment-added="(c) => emit('comment-added', c)"
                @comment-updated="(data) => emit('comment-updated', data)"
                @comment-deleted="(data) => emit('comment-deleted', data)"
                @comment-liked="(data) => emit('comment-liked', data)"
            />
        </div>
    </div>
</template>
