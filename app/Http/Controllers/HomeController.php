<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // ── 1. 상단 고정 공지 조회 (is_pinned=true, type=notice|pinned) ──────────
        $pinnedNotices = \App\Models\Post::with(['user.tier', 'category'])
            ->withCount(['comments', 'likes'])
            ->visible()
            ->pinnedNotice()          // scopePinnedNotice: type IN(notice,pinned) AND is_pinned=true
            ->latest()
            ->take(3)
            ->get();

        $pinnedIds = $pinnedNotices->pluck('id');

        $pinnedNotices = $pinnedNotices->map(function ($post) {
            return [
                'id'            => $post->id,
                'authorName'    => $post->user->name ?? '운영자',
                'authorAvatar'  => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
                'timeAgo'       => $post->created_at->diffForHumans(),
                'category'      => $post->category->name ?? '공지',
                'categorySlug'  => $post->category->slug ?? 'all',
                'tags'          => $post->tags ?? [],
                'title'         => $post->title,
                'summary'       => $post->summary,
                'likes'         => $post->likes_count ?? 0,
                'comments'      => $post->comments_count ?? 0,
                'views'         => $post->view_count,
                'type'          => $post->type,
                'isPinned'      => $post->is_pinned,
                'extra_info'    => $post->extra_info,
                'card_image_path' => $post->card_image_path,
                'card_image_url'  => $post->card_image_url,
                'authorTierName'  => $post->user->tier->name ?? '운영자',
                'authorTierIcon'  => $post->user->tier->icon_url ?? '🌱',
            ];
        });

        // ── 2. 일반 피드 조회 ───────────────────────────────────────────────────
        // - 상단에 고정된 공지(pinnedIds) 제외 → 중복 방지
        // - notice/pinned 타입 전체 제외 → 공지는 위 섹션에서만 보이도록
        $query = \App\Models\Post::with(['user.tier', 'category'])
            ->withCount(['comments', 'likes'])
            ->visible()
            ->whereNotIn('id', $pinnedIds)
            ->whereNotIn('type', ['notice', 'pinned']) // 공지는 피드에서 제외
            ->latest();

        // 카테고리 필터: 특정 카테고리 선택 시 해당 카테고리 글만 조회
        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($sub) use ($request) {
                $sub->where('slug', $request->category);
            });
        }

        if ($request->filled('search_keyword')) {
            $keyword = $request->search_keyword;
            $type = $request->input('search_type', 'title');

            if ($type === 'title') {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });
            } elseif ($type === 'tags') {
                $cleanKeyword = ltrim($keyword, '#');
                $query->whereRaw('LOWER(tags) like ?', ['%' . mb_strtolower($cleanKeyword) . '%']);
            } elseif ($type === 'author') {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            }
        }

        $paginator = $query->paginate(10)->appends($request->query());

        $posts = $paginator->getCollection()->map(function ($post) {
            $isBookmarked = auth()->check() ? $post->bookmarks()->where('user_id', auth()->id())->exists() : false;
            return [
                'id'             => $post->id,
                'authorName'     => $post->user->name ?? '탈퇴한 사용자',
                'authorAvatar'   => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
                'timeAgo'        => $post->created_at->diffForHumans(),
                'category'       => $post->category->name ?? '일반',
                'categorySlug'   => $post->category->slug ?? 'general',
                'tags'           => $post->tags ?? [],
                'title'          => $post->title,
                'summary'        => $post->summary,
                'likes'          => $post->likes_count ?? 0,
                'comments'       => $post->comments_count ?? 0,
                'views'          => $post->view_count,
                'type'           => $post->type,
                'isPinned'       => $post->is_pinned,
                'extra_info'     => $post->extra_info,
                'card_image_path' => $post->card_image_path,
                'card_image_url'  => $post->card_image_url,
                'isBookmarked'   => $isBookmarked,
                'authorTierName' => $post->user->tier->name ?? '씨앗',
                'authorTierIcon' => $post->user->tier->icon_url ?? '🌱',
            ];
        });

        $paginatedData = [
            'data'          => $posts,
            'next_page_url' => $paginator->nextPageUrl(),
        ];

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json($paginatedData);
        }

        $categories = \App\Models\Category::where('is_active', true)
            ->whereNotIn('slug', ['all', 'notice'])
            ->orderBy('sort_order')
            ->get();

        // 명예의 전당 (포인트 랭킹 상위 5명)
        $rankings = \App\Models\User::with('tier')
            ->where('role', '!=', 'master')
            ->orderBy('current_points', 'desc')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name'     => $user->name,
                    'points'   => $user->current_points,
                    'tierName' => $user->tier->name ?? '씨앗',
                    'tierIcon' => $user->tier->icon_url ?? '🌱',
                    'avatar'   => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
                ];
            });

        return \Inertia\Inertia::render('Home', [
            'posts'           => $paginatedData,
            'categories'      => $categories,
            'pinnedNotices'   => $pinnedNotices,
            'currentCategory' => $request->category ?? 'all',
            'filters'         => $request->only(['search_type', 'search_keyword']),
            'rankings'        => $rankings,
        ]);
    }


    public function popular()
    {
        $posts = \App\Models\Post::visible()->with(['user', 'category'])->orderBy('view_count', 'desc')->get()->map(function ($post) {
            $isBookmarked = auth()->check() ? $post->bookmarks()->where('user_id', auth()->id())->exists() : false;
            return [
            'id' => $post->id,
            'authorName' => $post->user->name ?? '탈퇴한 사용자',
            'authorAvatar' => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
            'timeAgo' => $post->created_at->diffForHumans(),
            'category' => $post->category->name ?? '일반',
            'categorySlug' => $post->category->slug ?? 'general',
            'tags' => $post->tags ?? [],
            'title' => $post->title,
            'summary' => $post->summary,
            'likes' => $post->view_count,
            'extra_info' => $post->extra_info,
            'card_image_path' => $post->card_image_path,
            'isBookmarked' => $isBookmarked,
            ];
        });



        $categories = \App\Models\Category::where('is_active', true)
            ->whereNotIn('slug', ['all', 'notice'])
            ->orderBy('sort_order')
            ->get();

        return \Inertia\Inertia::render('Popular', [
            'posts' => $posts,
            'categories' => $categories
        ]);
    }

    public function bookmarks()
    {
        if (!auth()->check()) {
            return redirect()->route('home', ['showLogin' => 1]);
        }

        $userId = auth()->id();
        $bookmarkedPosts = \App\Models\Post::visible()
        ->whereHas('bookmarks', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['user', 'category'])
        ->withCount(['comments', 'likes'])
        ->latest()
        ->get()
        ->map(function ($post) {
            return [
                'id' => $post->id,
                'authorName' => $post->user->name ?? '탈퇴한 사용자',
                'authorAvatar' => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
                'timeAgo' => $post->created_at->diffForHumans(),
                'category' => $post->category->name ?? '일반',
                'categorySlug' => $post->category->slug ?? 'general',
                'tags' => $post->tags ?? [],
                'title' => $post->title,
                'summary' => $post->summary,
                'likes' => $post->likes_count ?? 0,
                'comments' => $post->comments_count ?? 0,
                'views' => $post->view_count,
                'type' => $post->type,
                'isPinned' => $post->is_pinned,
                'isBookmarked' => true,
                'card_image_url' => $post->card_image_url,
            ];
        });

        $categories = \App\Models\Category::where('is_active', true)
            ->whereNotIn('slug', ['all', 'notice'])
            ->orderBy('sort_order')
            ->get();

        return \Inertia\Inertia::render('Bookmarks', [
            'posts' => $bookmarkedPosts,
            'categories' => $categories
        ]);
    }

}
