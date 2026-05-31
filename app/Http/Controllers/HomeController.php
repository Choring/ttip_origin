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
                $query->where('title', 'like', "%{$keyword}%");
            } elseif ($type === 'content') {
                // 본문 검색 — HTML 태그를 우회해 텍스트 키워드 매칭
                $query->where('content', 'like', "%{$keyword}%");
            } elseif ($type === 'title_content') {
                // 제목 + 본문 통합 검색
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%");
                });
            } elseif ($type === 'tags') {
                $cleanKeyword = mb_strtolower(ltrim($keyword, '#'));
                // post_tags 인덱스 테이블을 통한 빠른 검색
                $query->whereHas('postTags', function ($q) use ($cleanKeyword) {
                    $q->whereRaw('LOWER(tag) = ?', [$cleanKeyword]);
                });
            } elseif ($type === 'author') {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            }
        }

        $paginator = $query->paginate(10)->appends($request->query());

        // 본문 검색 시 매칭 위치 excerpt 추출
        $searchType    = $request->input('search_type', 'title');
        $searchKeyword = $request->input('search_keyword', '');
        $needsExcerpt  = $searchKeyword && in_array($searchType, ['content', 'title_content']);

        $posts = $paginator->getCollection()->map(function ($post) use ($needsExcerpt, $searchKeyword) {
            $isBookmarked = auth()->check() ? $post->bookmarks()->where('user_id', auth()->id())->exists() : false;

            // 본문 매칭 excerpt 생성 (검색어 앞뒤 60자)
            $contentExcerpt = null;
            if ($needsExcerpt) {
                $plainText = strip_tags($post->content ?? '');
                $plainText = html_entity_decode(preg_replace('/\s+/', ' ', $plainText));
                $pos = mb_stripos($plainText, $searchKeyword);
                if ($pos !== false) {
                    $start = max(0, $pos - 60);
                    $end   = min(mb_strlen($plainText), $pos + mb_strlen($searchKeyword) + 60);
                    $contentExcerpt = ($start > 0 ? '...' : '')
                        . mb_substr($plainText, $start, $end - $start)
                        . ($end < mb_strlen($plainText) ? '...' : '');
                }
            }

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
                'isBookmarked'    => $isBookmarked,
                'authorTierName'  => $post->user->tier->name ?? '씨앗',
                'authorTierIcon'  => $post->user->tier->icon_url ?? '🌱',
                'contentExcerpt'  => $contentExcerpt,
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

        // ── 대구 관광지 섹션 ──────────────────────────────────────────────────
        $featuredSpots = \Illuminate\Support\Facades\Cache::remember('home_featured_spots', 3600, function () {
            return \App\Models\TouristSpot::select('content_id', 'title', 'addr1', 'image', 'thumbnail')
                ->where(function ($q) {
                    $q->whereNotNull('image')->where('image', '!=', '')
                      ->orWhere(function ($q2) {
                          $q2->whereNotNull('thumbnail')->where('thumbnail', '!=', '');
                      });
                })
                ->inRandomOrder()
                ->take(8)
                ->get()
                ->map(fn($s) => [
                    'contentId' => $s->content_id,
                    'title'     => $s->title,
                    'addr1'     => $s->addr1,
                    'image'     => $s->image ?: $s->thumbnail,
                ]);
        });

        // ── 추천 맛집 섹션 ────────────────────────────────────────────────────
        $featuredRestaurants = \Illuminate\Support\Facades\Cache::remember('home_featured_restaurants', 3600, function () {
            return \App\Models\Restaurant::select('content_id', 'title', 'category', 'address', 'image')
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->inRandomOrder()
                ->take(10)
                ->get()
                ->map(fn($r) => [
                    'contentId' => $r->content_id,
                    'title'     => $r->title,
                    'category'  => $r->category,
                    'address'   => $r->address,
                    'image'     => $r->image,
                ]);
        });

        // ── 이번 주 공연·행사 섹션 ────────────────────────────────────────────
        $today     = now()->format('Ymd');
        $nextMonth = now()->addMonth()->format('Ymd');

        $upcomingEvents = \App\Models\CulturalEvent::whereRaw("REPLACE(end_date, '.', '') >= ?", [$today])
            ->whereRaw("REPLACE(start_date, '.', '') <= ?", [$nextMonth])
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->orderByRaw("REPLACE(start_date, '.', '') ASC")
            ->take(10)
            ->get()
            ->map(fn($e) => [
                'event_seq'  => $e->event_seq,
                'subject'    => $e->subject,
                'place'      => $e->place,
                'start_date' => $e->start_date,
                'end_date'   => $e->end_date,
                'image'      => $e->image,
                'event_gubun'=> $e->event_gubun,
                'pay'        => $e->pay,
            ]);

        // ── 히어로 배너용 오늘의 행사 ──────────────────────────────────────────
        $today = now()->format('Ymd');
        $heroEvent = \App\Models\CulturalEvent::whereRaw("REPLACE(start_date, '.', '') <= ?", [$today])
            ->whereRaw("REPLACE(end_date, '.', '') >= ?", [$today])
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->inRandomOrder()
            ->first();

        // 진행 중인 행사 없으면 곧 시작하는 행사
        if (!$heroEvent) {
            $heroEvent = \App\Models\CulturalEvent::whereRaw("REPLACE(start_date, '.', '') > ?", [$today])
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->orderByRaw("REPLACE(start_date, '.', '') ASC")
                ->first();
        }

        return \Inertia\Inertia::render('Home', [
            'posts'           => $paginatedData,
            'categories'      => $categories,
            'pinnedNotices'   => $pinnedNotices,
            'currentCategory' => $request->category ?? 'all',
            'filters'         => $request->only(['search_type', 'search_keyword']),
            'rankings'        => $rankings,
            'upcomingEvents'        => $upcomingEvents,
            'featuredRestaurants'   => $featuredRestaurants,
            'featuredSpots'         => $featuredSpots,
            'heroEvent'       => $heroEvent ? [
                'subject'    => $heroEvent->subject,
                'place'      => $heroEvent->place,
                'start_date' => $heroEvent->start_date,
                'end_date'   => $heroEvent->end_date,
                'image'      => $heroEvent->image,
                'event_seq'  => $heroEvent->event_seq,
            ] : null,
        ]);
    }


    public function popular()
    {
        $posts = \App\Models\Post::visible()
            ->with(['user', 'category'])
            ->withCount(['comments', 'likes'])
            ->orderBy('view_count', 'desc')
            ->get()
            ->map(function ($post) {
                $isBookmarked = auth()->check() ? $post->bookmarks()->where('user_id', auth()->id())->exists() : false;
                return [
                    'id'              => $post->id,
                    'authorName'      => $post->user->name ?? '탈퇴한 사용자',
                    'authorAvatar'    => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
                    'timeAgo'         => $post->created_at->diffForHumans(),
                    'category'        => $post->category->name ?? '일반',
                    'categorySlug'    => $post->category->slug ?? 'general',
                    'tags'            => $post->tags ?? [],
                    'title'           => $post->title,
                    'summary'         => $post->summary,
                    'likes'           => $post->likes_count ?? 0,
                    'comments'        => $post->comments_count ?? 0,
                    'views'           => $post->view_count ?? 0,
                    'extra_info'      => $post->extra_info,
                    'card_image_path' => $post->card_image_path,
                    'isBookmarked'    => $isBookmarked,
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

    public function tag(string $tag)
    {
        $posts = \App\Models\Post::visible()
            ->with(['user.tier', 'category'])
            ->withCount(['comments', 'likes'])
            ->whereHas('postTags', fn($q) => $q->where('tag', $tag))
            ->latest()
            ->paginate(20);

        $mapped = $posts->getCollection()->map(function ($post) {
            $isBookmarked = auth()->check() ? $post->bookmarks()->where('user_id', auth()->id())->exists() : false;
            return [
                'id'              => $post->id,
                'authorName'      => $post->user->name ?? '탈퇴한 사용자',
                'authorAvatar'    => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
                'timeAgo'         => $post->created_at->diffForHumans(),
                'category'        => $post->category->name ?? '일반',
                'categorySlug'    => $post->category->slug ?? 'general',
                'tags'            => $post->tags ?? [],
                'title'           => $post->title,
                'summary'         => $post->summary,
                'likes'           => $post->likes_count ?? 0,
                'comments'        => $post->comments_count ?? 0,
                'views'           => $post->view_count ?? 0,
                'extra_info'      => $post->extra_info,
                'card_image_path' => $post->card_image_path,
                'isBookmarked'    => $isBookmarked,
                'authorTierName'  => $post->user->tier->name ?? '',
                'authorTierIcon'  => $post->user->tier->icon_url ?? '',
            ];
        });

        $isSubscribed = auth()->check()
            ? \App\Models\TagSubscription::where('user_id', auth()->id())->where('tag', $tag)->exists()
            : false;

        return \Inertia\Inertia::render('Tag', [
            'tag'          => $tag,
            'posts'        => $mapped,
            'isSubscribed' => $isSubscribed,
            'pagination'   => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'total'        => $posts->total(),
            ],
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
