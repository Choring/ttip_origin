<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function store(StorePostRequest $request, \App\Services\PointService $pointService)
    {
        $validated = $request->validated();

        $validated['summary'] = ['요약 1', '요약 2']; // 임시 요약
        $validated['user_id'] = auth()->id();
        $validated['type'] = 'general';

        $post = Post::create($validated);

        // 글 작성 시 10 포인트 지급
        $pointService->addPoints(auth()->user(), 10, 'earn_post', 'posts', $post->id);

        return redirect()->route('home')->with('success', '게시글이 작성되었습니다. (+10 포인트)');
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $validated = $request->validated();

        $post->update($validated);

        return redirect()->back()->with('success', '게시글이 수정되었습니다.');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('home')->with('success', '게시글이 삭제되었습니다.');
    }
}
