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
        $categories = \App\Models\Category::where('is_active', true)
            ->where('slug', '!=', 'all')
            ->where('slug', 'not like', 'notice%')
            ->orderBy('sort_order')
            ->get();
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
        $isBookmarked = false;
        $likedCommentIds = [];
        if (auth()->check()) {
            $userId = auth()->id();
            $isLiked = $post->likes()->where('user_id', $userId)->exists();
            $isBookmarked = $post->bookmarks()->where('user_id', $userId)->exists();
            $likedCommentIds = \App\Models\CommentLike::whereIn('comment_id', $post->comments->pluck('id'))
                ->where('user_id', $userId)
                ->pluck('comment_id')
                ->toArray();
        }

        // 관련 게시글 추천 로직
        $tags = $post->tags ?: [];
        
        // 1순위: 같은 카테고리 + 태그가 하나라도 겹치는 글
        $relatedByTags = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('type', 'general')
            ->where(function ($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->orWhereJsonContains('tags', $tag);
                }
            })
            ->latest()
            ->take(4)
            ->get();

        $relatedIds = $relatedByTags->pluck('id');

        // 2순위: 태그 매칭이 부족할 경우 같은 카테고리 최신글로 보충
        $relatedPosts = $relatedByTags;
        if ($relatedPosts->count() < 4) {
            $fallbacks = Post::where('category_id', $post->category_id)
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedIds)
                ->where('type', 'general')
                ->latest()
                ->take(4 - $relatedPosts->count())
                ->get();
            
            $relatedPosts = $relatedPosts->concat($fallbacks);
        }

        return \Inertia\Inertia::render('Post/Show', [
            'post' => $post->load(['user.tier', 'comments.user.tier', 'category']),
            'isLiked' => $isLiked,
            'isBookmarked' => $isBookmarked,
            'likedCommentIds' => $likedCommentIds,
            'relatedPosts' => $relatedPosts->map(function($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'card_image_url' => $p->card_image_url,
                    'created_at' => $p->created_at,
                    'view_count' => $p->view_count,
                    'user' => ['name' => $p->user->name]
                ];
            })
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

        // 글 작성 포인트 지급 (+50P)
        /** @var \App\Models\User $author */
        $author = auth()->user();
        $pointService->addPoints($author, 50, 'create_post', 'posts', $post->id);
        session()->flash('point_gain', 50);

        return redirect()->route('home')->with('success', '게시글이 깔끔하게 작성되었습니다.');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = \App\Models\Category::where('is_active', true)
            ->where('slug', '!=', 'all')
            ->where('slug', 'not like', 'notice%')
            ->orderBy('sort_order')
            ->get();
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

    public function destroy(Post $post, \App\Services\PointService $pointService)
    {
        Gate::authorize('delete', $post);

        // 게시글 관련 포인트 회수 (삭제 전에 실행)
        $pointService->revokePostPoints($post);

        $post->delete();

        return redirect()->route('home')->with('success', '게시글이 삭제되었습니다.');
    }

    public function toggleLike(Post $post, \App\Services\PointService $pointService)
    {
        $user = auth()->user();
        $postAuthor = $post->user;

        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('likes_count');
            
            // 띱 취소 시 작성자 포인트 회수 (본인 글 제외)
            if ($postAuthor && $postAuthor->id !== $user->id) {
                $pointService->subtractPoints($postAuthor, 5, 'lost_like', 'posts', $post->id);
            }
            
            $message = '게시글에 남긴 띱 👍을 슬그머니 취소했습니다.';
        } else {
            $post->likes()->create([
                'user_id' => $user->id
            ]);
            $post->increment('likes_count');
            
            // 띱 수신 시 작성자 포인트 적립 (본인 글 제외)
            if ($postAuthor && $postAuthor->id !== $user->id) {
                $pointService->addPoints($postAuthor, 5, 'receive_like', 'posts', $post->id);
                session()->flash('point_gain', 5);

                // 알림 발송
                $postAuthor->notify(new \App\Notifications\LikedNotification($post, $user));
            }


            $message = '이 글에 강렬한 띱 👍을 날렸습니다!';
        }

        return back()->with('success', $message);
    }

    public function toggleBookmark(Post $post)
    {
        $user = auth()->user();

        $existingBookmark = $post->bookmarks()->where('user_id', $user->id)->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            $message = '북마크 리스트에서 작별을 고했습니다. 🔖';
        } else {
            $post->bookmarks()->create([
                'user_id' => $user->id
            ]);
            $message = '나중에 다시 만날 수 있도록 북마크에 담았습니다. 🔖';
        }

        return back()->with('success', $message);
    }
}

