<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    comment: Object,
    postId: Number,
    depth: {
        type: Number,
        default: 0
    }
});

const page = usePage();
const user = page.props.auth.user;

const showReplyForm = ref(false);
const showEditForm = ref(false);

const replyForm = useForm({
    content: '',
    parent_id: props.comment.id,
});

const editForm = useForm({
    content: props.comment.content,
});

const deleteForm = useForm({});

const submitReply = () => {
    replyForm.post(route('comments.store', { post: props.postId }), {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset('content');
            showReplyForm.value = false;
        }
    });
};

const submitEdit = () => {
    editForm.put(route('comments.update', { comment: props.comment.id }), {
        preserveScroll: true,
        onSuccess: () => {
            showEditForm.value = false;
        }
    });
};

const deleteComment = () => {
    if (confirm('정말로 이 댓글을 삭제하시겠습니까? (하위 대댓글도 모두 삭제됩니다)')) {
        deleteForm.delete(route('comments.destroy', { comment: props.comment.id }), {
            preserveScroll: true
        });
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

const emit = defineEmits(['open-login-modal']);

const likeForm = useForm({});

const toggleLike = () => {
    if (!user) {
        emit('open-login-modal', 'like');
        return;
    }

    likeForm.post(route('comments.like', { comment: props.comment.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="mb-4">
        <div class="flex space-x-3">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full object-cover" :src="'https://ui-avatars.com/api/?name='+encodeURI(comment.user?.name || '?')+'&background=random'" alt="" />
            </div>
            
            <div class="flex-grow max-w-full">
                <div class="bg-gray-50 rounded-2xl px-4 py-3 shadow-sm border border-gray-100">
                    <div class="mb-1">
                        <!-- 닉네임 -->
                        <div class="flex items-center space-x-1 font-bold text-sm text-gray-900">
                            <span v-if="comment.user?.tier?.icon_url" :title="comment.user.tier.name" class="text-xs">{{ comment.user.tier.icon_url }}</span>
                            <span>{{ comment.user?.name || '알 수 없음' }}</span>
                        </div>
                        <!-- 날짜 -->
                        <span class="text-[11px] text-gray-400">{{ formatDate(comment.created_at) }}</span>
                    </div>
                    
                    <div v-if="!showEditForm" class="text-gray-800 text-sm whitespace-pre-wrap leading-relaxed break-words">
                        {{ comment.content }}
                    </div>
                    
                    <form v-else @submit.prevent="submitEdit" class="mt-2 text-right">
                        <textarea v-model="editForm.content" rows="2" class="w-full p-2 text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                        <div class="mt-2 space-x-2">
                            <button type="button" @click="showEditForm = false" class="text-xs font-semibold text-gray-500 hover:text-gray-700 transition">취소</button>
                            <button type="submit" class="text-xs font-semibold bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700 transition" :disabled="editForm.processing">수정 완료</button>
                        </div>
                    </form>
                </div>
                
                <!-- Actions -->
                <div class="mt-1 ml-2 flex items-center space-x-4 text-xs text-gray-400 font-semibold">
                    <button @click="toggleLike" class="flex items-center space-x-1 transition" :class="comment.isLiked ? 'text-indigo-600' : 'hover:text-indigo-600'">
                        <span>좋아요</span>
                        <span v-if="comment.likes_count > 0">{{ comment.likes_count }}</span>
                    </button>
                    <button v-if="user" @click="showReplyForm = !showReplyForm" class="hover:text-indigo-600 transition">답글 달기</button>
                    <button v-if="user && user.id === comment.user_id" @click="showEditForm = !showEditForm" class="hover:text-amber-600 transition">수정</button>
                    <button v-if="user && user.id === comment.user_id" @click="deleteComment" class="hover:text-red-600 transition">삭제</button>
                </div>
                
                <!-- Reply Form -->
                <form v-if="showReplyForm && user" @submit.prevent="submitReply" class="mt-3 relative flex items-center">
                    <img class="absolute left-2 h-7 w-7 rounded-full object-cover" :src="'https://ui-avatars.com/api/?name='+encodeURI(user.name)+'&background=random'" alt="" />
                    <input v-model="replyForm.content" type="text" placeholder="답글을 입력하세요..." class="w-full pl-11 pr-20 py-2.5 border border-gray-200 rounded-full text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-inner" required />
                    <button type="submit" class="absolute right-1 bottom-1 top-1 px-4 bg-indigo-600 hover:bg-indigo-700 transition text-white text-xs font-semibold rounded-full" :disabled="replyForm.processing">작성</button>
                </form>
            </div>
        </div>

        <!-- Nested Comments (Moved OUTSIDE the flex wrapper so it doesn't infinitely indent) -->
        <div v-if="comment.children && comment.children.length > 0" 
             :class="['mt-3', depth === 0 ? 'ml-12 border-l-2 border-indigo-100 pl-4 py-1' : '']">
            <CommentItem 
                v-for="child in comment.children" 
                :key="child.id" 
                :comment="child" 
                :postId="postId" 
                :depth="depth + 1" 
                @open-login-modal="(type) => emit('open-login-modal', type)"
            />
        </div>
    </div>
</template>
