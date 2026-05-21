<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PointService;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Post $post, PointService $pointService)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // 1. 질 높은 댓글 유도를 위해 10자 이상 작성 시 본인에게 1 포인트 지급
        if (mb_strlen($validated['content']) >= 10) {
            $pointService->addPoints($user, 1, 'earn_comment_effort', 'comments', $comment->id);
            session()->flash('point_gain', 1); // 토스트 알림용
        }

        $postAuthor = $post->user;

        if ($comment->parent_id) {
            // 답글인 경우: 원 댓글 작성자에게 알림 (본인 제외)
            $parentComment = \App\Models\Comment::find($comment->parent_id);
            if ($parentComment && $parentComment->user_id !== $user->id) {
                $parentComment->user->notify(new \App\Notifications\CommentReplied($parentComment, $comment));
            }
        } else {
            // 일반 댓글인 경우: 원글 작성자에게 포인트 + 알림 (본인 제외)
            if ($postAuthor && $postAuthor->id !== $user->id) {
                $pointService->addPoints($postAuthor, 3, 'receive_comment', 'comments', $comment->id);
                session()->flash('point_gain', 3);
                $postAuthor->notify(new \App\Notifications\PostCommented($post, $comment));
            }
        }

        return redirect()->back();

    }

    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update($validated);

        return redirect()->back();
    }

    public function destroy(Comment $comment, PointService $pointService)
    {
        Gate::authorize('delete', $comment);

        // 댓글 관련 포인트 회수 (삭제 전에 실행)
        $pointService->revokeCommentPoints($comment);

        // 대댓글도 cascade 삭제되므로 각각 포인트 회수
        $comment->replies()->each(function ($reply) use ($pointService) {
            $pointService->revokeCommentPoints($reply);
        });

        $comment->delete();

        return redirect()->back();
    }

    public function toggleLike(Comment $comment, \App\Services\PointService $pointService)
    {
        $user = auth()->user();
        $commentAuthor = $comment->user;

        $existingLike = $comment->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $comment->decrement('likes_count');
            
            // 띱 취소 시 작성자 포인트 회수 (본인 댓글 제외)
            if ($commentAuthor && $commentAuthor->id !== $user->id) {
                $pointService->subtractPoints($commentAuthor, 5, 'lost_comment_like', 'comments', $comment->id);
            }
            
            $message = '댓글 좋아요를 취소했습니다.';
        } else {
            $comment->likes()->create([
                'user_id' => $user->id
            ]);
            $comment->increment('likes_count');
            
            // 띱 수신 시 작성자 포인트 적립 (본인 댓글 제외)
            if ($commentAuthor && $commentAuthor->id !== $user->id) {
                $pointService->addPoints($commentAuthor, 5, 'receive_comment_like', 'comments', $comment->id);
                session()->flash('point_gain', 5);

                // 알림 발송
                $commentAuthor->notify(new \App\Notifications\LikedNotification($comment, $user));
            }

            $message = '댓글 좋아요를 눌렀습니다!';
        }

        return back()->with('success', $message);
    }
}
