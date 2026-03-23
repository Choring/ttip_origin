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
        // 글 작성 시 2 포인트 지급
        $pointService->addPoints($user, 2, 'earn_comment', 'comments', $comment->id);

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
