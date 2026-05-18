<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Response;

class SEOController extends Controller
{
    public function sitemap()
    {
        // 공지 전용 카테고리(all)는 제외, 일반 카테고리만
        $categories = Category::where('is_active', true)
            ->where('slug', '!=', 'all')
            ->get();

        // 공지/광고 타입 제외, 일반 게시글만, 최근 500개
        $posts = Post::with('category')
            ->whereIn('type', ['general'])
            ->latest('updated_at')
            ->take(500)
            ->get();

        return response()->view('seo.sitemap', [
            'posts'      => $posts,
            'categories' => $categories,
        ])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
