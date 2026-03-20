<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

class PostController extends Controller
{
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        $validated['summary'] = ['요약 1', '요약 2']; // 임시 요약
        $validated['user_id'] = auth()->id();
        $validated['type'] = 'general';

        Post::create($validated);

        return redirect()->route('home')->with('success', '게시글이 작성되었습니다.');
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $post->update($validated);

        return redirect()->back()->with('success', '게시글이 수정되었습니다.');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('home')->with('success', '게시글이 삭제되었습니다.');
    }
}
