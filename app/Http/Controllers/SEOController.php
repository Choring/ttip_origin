<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Restaurant;
use App\Models\TouristSpot;
use Illuminate\Http\Response;

class SEOController extends Controller
{
    public function sitemapIndex()
    {
        $postCount = Post::whereIn('type', ['general'])->count();
        $perPage = 1000;
        $pages = ceil($postCount / $perPage);

        return response()->view('seo.sitemap-index', [
            'pages' => $pages
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function sitemapMain()
    {
        $categories = Category::where('is_active', true)
            ->where('slug', '!=', 'all')
            ->get();

        return response()->view('seo.sitemap-main', [
            'categories' => $categories,
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function sitemapPosts($page)
    {
        $perPage = 1000;
        $posts = Post::with('category')
            ->whereIn('type', ['general'])
            ->latest('id')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->view('seo.sitemap-posts', [
            'posts' => $posts,
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function sitemapSpots()
    {
        $spots = TouristSpot::select('content_id', 'image', 'updated_at')->get();

        return response()->view('seo.sitemap-spots', [
            'spots' => $spots,
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function sitemapRestaurants()
    {
        $restaurants = Restaurant::select('content_id', 'image', 'updated_at')->get();

        return response()->view('seo.sitemap-restaurants', [
            'restaurants' => $restaurants,
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
