<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Response;

class SEOController extends Controller
{
    public function sitemap()
    {
        $posts = Post::latest()->get();
        $categories = Category::where('is_active', true)->get();

        return response()->view('seo.sitemap', [
            'posts' => $posts,
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}
