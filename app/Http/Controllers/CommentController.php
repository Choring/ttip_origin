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

        // 2. 내 글에 댓글이 달렸을 때 원글 작성자에게 3 포인트 지급 (본인 댓글 제외)
        $postAuthor = $post->user;
        if ($postAuthor && $postAuthor->id !== $user->id) {
            $pointService->addPoints($postAuthor, 3, 'receive_comment', 'comments', $comment->id);
            // 원글 작성자가 현재 접속 중인 경우 Inertia를 통해 알림이 전달되도록 세션 플래시
            session()->flash('point_gain', 3);
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

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        // Delete sub-comments recursively ?
        // The migration has cascadeOnDelete for parent_id, so it deletes automatically in DB.
        $comment->delete();

        return redirect()->back();
    }
}
