<?php

namespace App\Http\Controllers;

use App\Models\CulturalEvent;
use App\Models\Post;
use App\Models\Category;
use App\Models\Restaurant;
use App\Models\TouristSpot;
use Illuminate\Support\Facades\Cache;

class SEOController extends Controller
{
    /** 사이트맵 인덱스 (sub-sitemap 목록) */
    public function sitemapIndex()
    {
        $pages = Cache::remember('sitemap_post_pages', 3600, function () {
            $count = Post::visible()->where('type', 'general')->count();
            return max(1, (int) ceil($count / 1000));
        });

        return response()
            ->view('seo.sitemap-index', ['pages' => $pages])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /** 정적 페이지 + 카테고리 사이트맵 */
    public function sitemapMain()
    {
        $categories = Cache::remember('sitemap_categories', 3600, fn() =>
            Category::where('is_active', true)
                ->whereNotIn('slug', ['all', 'notice'])
                ->orderBy('sort_order')
                ->get(['slug', 'updated_at'])
        );

        return response()
            ->view('seo.sitemap-main', ['categories' => $categories])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /** 게시글 사이트맵 (1000개 단위 페이징) */
    public function sitemapPosts(int $page)
    {
        $perPage = 1000;

        $posts = Cache::remember("sitemap_posts_{$page}", 1800, fn() =>
            Post::visible()
                ->with('category:id,name')
                ->where('type', 'general')
                ->latest('id')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get(['id', 'title', 'card_image_path', 'updated_at'])
        );

        return response()
            ->view('seo.sitemap-posts', ['posts' => $posts])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=1800');
    }

    /** 관광지 사이트맵 */
    public function sitemapSpots()
    {
        $spots = Cache::remember('sitemap_spots', 86400, fn() =>
            TouristSpot::select('content_id', 'title', 'image', 'updated_at')->get()
        );

        return response()
            ->view('seo.sitemap-spots', ['spots' => $spots])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /** 문화행사 사이트맵 (1년 이내 종료 또는 진행 중) */
    public function sitemapEvents()
    {
        $events = Cache::remember('sitemap_events', 3600, fn() =>
            CulturalEvent::select('event_seq', 'title', 'image', 'updated_at')
                ->where('end_date', '>=', now()->subYear()->toDateString())
                ->get()
        );

        return response()
            ->view('seo.sitemap-events', ['events' => $events])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /** 맛집 사이트맵 */
    public function sitemapRestaurants()
    {
        $restaurants = Cache::remember('sitemap_restaurants', 86400, fn() =>
            Restaurant::select('content_id', 'title', 'image', 'updated_at')->get()
        );

        return response()
            ->view('seo.sitemap-restaurants', ['restaurants' => $restaurants])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /** RSS 피드 */
    public function rss()
    {
        $posts = Cache::remember('rss_feed', 900, fn() =>
            Post::with(['user:id,name', 'category:id,name'])
                ->visible()
                ->where('type', 'general')
                ->latest('id')
                ->take(50)
                ->get(['id', 'title', 'summary', 'card_image_path', 'user_id', 'category_id', 'created_at', 'updated_at'])
        );

        return response()
            ->view('seo.rss', [
                'posts'       => $posts,
                'siteUrl'     => config('app.url'),
                'siteName'    => 'ttip',
                'description' => '대구 관광지, 맛집, 문화행사 정보와 지역 커뮤니티. 대구의 매력을 ttip에서 발견하세요.',
                'buildDate'   => now()->toRfc2822String(),
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=900');
    }

    /** robots.txt */
    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = <<<ROBOTS
        User-agent: *
        Allow: /

        # 관리자 · 인증 영역 크롤링 차단
        Disallow: /admin/
        Disallow: /dashboard
        Disallow: /profile/
        Disallow: /login
        Disallow: /register
        Disallow: /forgot-password
        Disallow: /reset-password
        Disallow: /api/

        # 검색 결과 페이지 파라미터 제한 (중복 콘텐츠 방지)
        Disallow: /*?*search_keyword=*

        # Google AdSense 크롤러 명시적 허용
        User-agent: Mediapartners-Google
        Allow: /

        # Googlebot 명시적 허용
        User-agent: Googlebot
        Allow: /

        Sitemap: {$sitemapUrl}
        ROBOTS;

        // 들여쓰기 제거
        $content = preg_replace('/^[ \t]+/m', '', $content);

        return response($content, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
