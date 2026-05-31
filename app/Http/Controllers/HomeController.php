<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Models\CulturalEvent;
use App\Models\Restaurant;
use App\Models\TouristSpot;

class HomeController extends Controller
{
    // ── 포털 메인 페이지 (/home) ────────────────────────────────────────────
    public function index()
    {
        $today     = now()->format('Ymd');
        $nextMonth = now()->addMonth()->format('Ymd');

        // 히어로 배너 행사
        $heroEvent = CulturalEvent::whereRaw("REPLACE(start_date, '.', '') <= ?", [$today])
            ->whereRaw("REPLACE(end_date, '.', '') >= ?", [$today])
            ->whereNotNull('image')->where('image', '!=', '')
            ->inRandomOrder()->first();

        if (!$heroEvent) {
            $heroEvent = CulturalEvent::whereRaw("REPLACE(start_date, '.', '') > ?", [$today])
                ->whereNotNull('image')->where('image', '!=', '')
                ->orderByRaw("REPLACE(start_date, '.', '') ASC")->first();
        }

        // 이번 주 공연·행사
        $upcomingEvents = CulturalEvent::whereRaw("REPLACE(end_date, '.', '') >= ?", [$today])
            ->whereRaw("REPLACE(start_date, '.', '') <= ?", [$nextMonth])
            ->whereNotNull('image')->where('image', '!=', '')
            ->orderByRaw("REPLACE(start_date, '.', '') ASC")
            ->take(10)->get()
            ->map(fn($e) => [
                'event_seq'   => $e->event_seq,
                'subject'     => $e->subject,
                'place'       => $e->place,
                'start_date'  => $e->start_date,
                'end_date'    => $e->end_date,
                'image'       => $e->image,
                'event_gubun' => $e->event_gubun,
                'pay'         => $e->pay,
            ]);

        // 추천 맛집
        $featuredRestaurants = Cache::remember('home_featured_restaurants', 3600, function () {
            return Restaurant::select('content_id', 'title', 'category', 'address', 'image')
                ->whereNotNull('image')->where('image', '!=', '')
                ->inRandomOrder()->take(10)->get()
                ->map(fn($r) => [
                    'contentId' => $r->content_id,
                    'title'     => $r->title,
                    'category'  => $r->category,
                    'address'   => $r->address,
                    'image'     => $r->image,
                ]);
        });

        // 대구 관광지
        $featuredSpots = Cache::remember('home_featured_spots', 3600, function () {
            return TouristSpot::select('content_id', 'title', 'addr1', 'image', 'thumbnail')
                ->where(function ($q) {
                    $q->whereNotNull('image')->where('image', '!=', '')
                      ->orWhere(fn($q2) => $q2->whereNotNull('thumbnail')->where('thumbnail', '!=', ''));
                })
                ->inRandomOrder()->take(8)->get()
                ->map(fn($s) => [
                    'contentId' => $s->content_id,
                    'title'     => $s->title,
                    'addr1'     => $s->addr1,
                    'image'     => $s->image ?: $s->thumbnail,
                ]);
        });

        // 커뮤니티 최신글 5개
        $recentPosts = Post::with(['user.tier', 'category'])
            ->withCount(['comments', 'likes'])
            ->visible()
            ->whereNotIn('type', ['notice', 'pinned'])
            ->latest()->take(5)->get()
            ->map(fn($post) => $this->mapPost($post));

        return \Inertia\Inertia::render('Home', [
            'heroEvent'           => $heroEvent ? [
                'subject'    => $heroEvent->subject,
                'place'      => $heroEvent->place,
                'start_date' => $heroEvent->start_date,
                'end_date'   => $heroEvent->end_date,
                'image'      => $heroEvent->image,
                'event_seq'  => $heroEvent->event_seq,
            ] : null,
            'upcomingEvents'      => $upcomingEvents,
            'featuredRestaurants' => $featuredRestaurants,
            'featuredSpots'       => $featuredSpots,
            'recentPosts'         => $recentPosts,
        ]);
    }

    // ── 커뮤니티 피드 페이지 (/community) ──────────────────────────────────
    public function community(Request $request)
    {
        // 상단 고정 공지
        $pinnedNotices = Post::with(['user.tier', 'category'])
            ->withCount(['comments', 'likes'])
            ->visible()->pinnedNotice()->latest()->take(3)->get();

        $pinnedIds = $pinnedNotices->pluck('id');

        $pinnedNotices = $pinnedNotices->map(fn($post) => [
            'id'             => $post->id,
            'authorName'     => $post->user->name ?? '운영자',
            'authorAvatar'   => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
            'timeAgo'        => $post->created_at->diffForHumans(),
            'category'       => $post->category->name ?? '공지',
            'categorySlug'   => $post->category->slug ?? 'all',
            'tags'           => $post->tags ?? [],
            'title'          => $post->title,
            'summary'        => $post->summary,
            'likes'          => $post->likes_count ?? 0,
            'comments'       => $post->comments_count ?? 0,
            'views'          => $post->view_count,
            'type'           => $post->type,
            'isPinned'       => $post->is_pinned,
            'extra_info'     => $post->extra_info,
            'card_image_url' => $post->card_image_url,
            'authorTierName' => $post->user->tier->name ?? '운영자',
            'authorTierIcon' => $post->user->tier->icon_url ?? '🌱',
        ]);

        // 피드 쿼리
        $query = Post::with(['user.tier', 'category'])
            ->withCount(['comments', 'likes'])
            ->visible()
            ->whereNotIn('id', $pinnedIds)
            ->whereNotIn('type', ['notice', 'pinned'])
            ->latest();

        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('category', fn($sub) => $sub->where('slug', $request->category));
        }

        if ($request->filled('search_keyword')) {
            $keyword = $request->search_keyword;
            $type    = $request->input('search_type', 'title');

            match ($type) {
                'title'         => $query->where('title', 'like', "%{$keyword}%"),
                'content'       => $query->where('content', 'like', "%{$keyword}%"),
                'title_content' => $query->where(fn($q) => $q->where('title', 'like', "%{$keyword}%")->orWhere('content', 'like', "%{$keyword}%")),
                'tags'          => $query->whereHas('postTags', fn($q) => $q->whereRaw('LOWER(tag) = ?', [mb_strtolower(ltrim($keyword, '#'))])),
                'author'        => $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$keyword}%")),
                default         => null,
            };
        }

        $paginator = $query->paginate(10)->appends($request->query());

        $searchType    = $request->input('search_type', 'title');
        $searchKeyword = $request->input('search_keyword', '');
        $needsExcerpt  = $searchKeyword && in_array($searchType, ['content', 'title_content']);

        $posts = $paginator->getCollection()->map(function ($post) use ($needsExcerpt, $searchKeyword) {
            $data           = $this->mapPost($post);
            $data['isBookmarked']   = auth()->check() ? $post->bookmarks()->where('user_id', auth()->id())->exists() : false;
            $data['contentExcerpt'] = null;

            if ($needsExcerpt) {
                $plainText = html_entity_decode(preg_replace('/\s+/', ' ', strip_tags($post->content ?? '')));
                $pos = mb_stripos($plainText, $searchKeyword);
                if ($pos !== false) {
                    $start = max(0, $pos - 60);
                    $end   = min(mb_strlen($plainText), $pos + mb_strlen($searchKeyword) + 60);
                    $data['contentExcerpt'] = ($start > 0 ? '...' : '')
                        . mb_substr($plainText, $start, $end - $start)
                        . ($end < mb_strlen($plainText) ? '...' : '');
                }
            }

            return $data;
        });

        $paginatedData = [
            'data'          => $posts,
            'next_page_url' => $paginator->nextPageUrl(),
        ];

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json($paginatedData);
        }

        $categories = Category::where('is_active', true)
            ->whereNotIn('slug', ['all', 'notice'])
            ->orderBy('sort_order')->get();

        $rankings = User::with('tier')->where('role', '!=', 'master')
            ->orderBy('current_points', 'desc')->take(5)->get()
            ->map(fn($u) => [
                'name'     => $u->name,
                'points'   => $u->current_points,
                'tierName' => $u->tier->name ?? '씨앗',
                'tierIcon' => $u->tier->icon_url ?? '🌱',
                'avatar'   => 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=random',
            ]);

        return \Inertia\Inertia::render('Community', [
            'posts'           => $paginatedData,
            'categories'      => $categories,
            'pinnedNotices'   => $pinnedNotices,
            'currentCategory' => $request->category ?? 'all',
            'filters'         => $request->only(['search_type', 'search_keyword']),
            'rankings'        => $rankings,
        ]);
    }

    // ── 공통 Post 매핑 ──────────────────────────────────────────────────────
    private function mapPost(Post $post): array
    {
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
            'views'           => $post->view_count,
            'type'            => $post->type,
            'isPinned'        => $post->is_pinned,
            'extra_info'      => $post->extra_info,
            'card_image_path' => $post->card_image_path,
            'card_image_url'  => $post->card_image_url,
            'authorTierName'  => $post->user->tier->name ?? '씨앗',
            'authorTierIcon'  => $post->user->tier->icon_url ?? '🌱',
            'isBookmarked'    => false,
            'contentExcerpt'  => null,
        ];
    }

    public function popular()
    {
        $posts = Post::visible()
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

        $categories = Category::where('is_active', true)
            ->whereNotIn('slug', ['all', 'notice'])
            ->orderBy('sort_order')->get();

        return \Inertia\Inertia::render('Popular', [
            'posts'      => $posts,
            'categories' => $categories,
        ]);
    }

    public function tag(string $tag)
    {
        $posts = Post::visible()
            ->with(['user.tier', 'category'])
            ->withCount(['comments', 'likes'])
            ->whereHas('postTags', fn($q) => $q->where('tag', $tag))
            ->latest()->paginate(20);

        $mapped = $posts->getCollection()->map(function ($post) {
            $data = $this->mapPost($post);
            $data['isBookmarked'] = auth()->check() ? $post->bookmarks()->where('user_id', auth()->id())->exists() : false;
            return $data;
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
        $bookmarkedPosts = Post::visible()
            ->whereHas('bookmarks', fn($q) => $q->where('user_id', $userId))
            ->with(['user', 'category'])
            ->withCount(['comments', 'likes'])
            ->latest()->get()
            ->map(function ($post) {
                $data = $this->mapPost($post);
                $data['isBookmarked'] = true;
                return $data;
            });

        $categories = Category::where('is_active', true)
            ->whereNotIn('slug', ['all', 'notice'])
            ->orderBy('sort_order')->get();

        return \Inertia\Inertia::render('Bookmarks', [
            'posts'      => $bookmarkedPosts,
            'categories' => $categories,
        ]);
    }
}
