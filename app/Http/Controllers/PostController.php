<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();
        return \Inertia\Inertia::render('Post/Create', [
            'categories' => $categories
        ]);
    }

    public function show(Post $post)
    {
        // 조회수 어뷰징 방지: 한 세션(브라우저 접속) 당 게시글별로 1회만 카운트되도록 처리
        $sessionKey = 'post_viewed_' . $post->id;
        if (!session()->has($sessionKey)) {
            $post->increment('view_count');
            session()->put($sessionKey, true);
        }

        $isLiked = false;
        if (auth()->check()) {
            $isLiked = $post->likes()->where('user_id', auth()->id())->exists();
        }

        return \Inertia\Inertia::render('Post/Show', [
            'post' => $post->load(['user', 'comments.user', 'category']),
            'isLiked' => $isLiked
        ]);
    }

    public function store(StorePostRequest $request, \App\Services\PointService $pointService)
    {
        $validated = $request->validated();

        // 본문을 줄바꿈(\n) 단위로 나누고 빈 줄은 제거한 뒤 최대 3줄까지만 요약으로 추출합니다.
        $lines = array_values(array_filter(array_map('trim', explode("\n", $validated['content']))));
        $summary = array_slice($lines, 0, 3);

        if (empty($summary)) {
            $summary = [mb_substr($validated['content'], 0, 50) . '...']; // 한 줄로 너무 길게 쓴 경우 첫 50자만 사용
        }

        $validated['summary'] = $summary;
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['card_image_path'] = \App\Helpers\FileUploadHelper::upload($request->file('image'), 'posts');
        }

        $post = Post::create($validated);

        // 글 작성 시 10 포인트 지급
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $pointService->addPoints($user, 10, 'earn_post', 'posts', $post->id);

        return redirect()->route('home')->with('success', '게시글이 작성되었습니다. (+10 포인트)');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();
        return \Inertia\Inertia::render('Post/Edit', [
            'post' => $post,
            'categories' => $categories
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $validated = $request->validated();

        // Update summary if content changed
        $lines = array_values(array_filter(array_map('trim', explode("\n", $validated['content']))));
        $summary = array_slice($lines, 0, 3);
        if (empty($summary)) {
            $summary = [mb_substr($validated['content'], 0, 50) . '...'];
        }
        $validated['summary'] = $summary;

        $post->update($validated);

        return redirect()->route('posts.show', $post)->with('success', '게시글이 깔끔하게 수정되었습니다.');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('home')->with('success', '게시글이 삭제되었습니다.');
    }

    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('likes_count');
            $message = '게시글에 남긴 띱 👍을 슬그머니 취소했습니다.';
        } else {
            $post->likes()->create([
                'user_id' => $user->id
            ]);
            $post->increment('likes_count');
            $message = '이 글에 강렬한 띱 👍을 날렸습니다!';
        }

        return back()->with('success', $message);
    }
}
