<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Post::with(['user', 'category'])->latest();

        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $posts = $query->get()->map(function($post) {
            return [
                'id' => $post->id,
                'authorName' => $post->user->name ?? '탈퇴한 사용자',
                'authorAvatar' => 'https://ui-avatars.com/api/?name='.urlencode($post->user->name ?? '?').'&background=random',
                'timeAgo' => $post->created_at->diffForHumans(),
                'category' => $post->category->name ?? '일반',
                'tag' => $post->category ? '' : '미분류',
                'title' => $post->title,
                'summary' => $post->summary,
                'likes' => $post->view_count,
            ];
        });

        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();

        return \Inertia\Inertia::render('Home', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $request->category ?? 'all'
        ]);
    }

    public function popular()
    {
        $posts = \App\Models\Post::with('user')->orderBy('view_count', 'desc')->get()->map(function($post) {
            return [
                'id' => $post->id,
                'authorName' => $post->user->name ?? '탈퇴한 사용자',
                'authorAvatar' => 'https://ui-avatars.com/api/?name='.urlencode($post->user->name ?? '?').'&background=random',
                'timeAgo' => $post->created_at->diffForHumans(),
                'category' => '일반',
                'tag' => '인기',
                'title' => $post->title,
                'summary' => $post->summary,
                'likes' => $post->view_count,
            ];
        });

        return \Inertia\Inertia::render('Popular', [
            'posts' => $posts
        ]);
    }

    public function bookmarks()
    {
        return \Inertia\Inertia::render('Bookmarks', [
            'posts' => []
        ]);
    }
}
