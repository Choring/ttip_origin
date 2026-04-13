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

        // extra_info 처리 (카테고리별 정형 데이터)
        $validated['extra_info'] = $request->input('extra_info', []);
        
        $validated['user_id'] = auth()->id();

        // 관리자가 아닌 경우 type과 is_pinned 필드는 기본값으로 강제 고정
        if (!in_array(auth()->user()->role, ['master', 'admin'])) {
            $validated['type'] = 'general';
            $validated['is_pinned'] = false;
        } else {
            // 관리자인 경우 요청받은 값을 사용 (null인 경우 기본값 처리)
            $validated['type'] = $request->input('type', 'general');
            $validated['is_pinned'] = $request->boolean('is_pinned', false);
        }

        if ($request->hasFile('image')) {
            $validated['card_image_path'] = \App\Helpers\FileUploadHelper::upload($request->file('image'), 'posts');
        }

        $post = Post::create($validated);

        // 글 작성 시 10 포인트 지급 (단, 공지사항은 포인트 지급 제외)
        if ($post->type !== 'notice') {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $pointService->addPoints($user, 10, 'earn_post', 'posts', $post->id);
        }

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

        // extra_info 처리
        $validated['extra_info'] = $request->input('extra_info', $post->extra_info);

        // 관리자가 아닌 경우 type과 is_pinned 수정 불가
        if (!in_array(auth()->user()->role, ['master', 'admin'])) {
            unset($validated['type'], $validated['is_pinned']);
        } else {
            $validated['type'] = $request->input('type', $post->type);
            $validated['is_pinned'] = $request->boolean('is_pinned', $post->is_pinned);
        }

        if ($request->hasFile('image')) {
            $validated['card_image_path'] = \App\Helpers\FileUploadHelper::upload($request->file('image'), 'posts');
        }

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
