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

        if ($request->filled('search_keyword')) {
            $keyword = $request->search_keyword;
            $type = $request->input('search_type', 'title');

            if ($type === 'title') {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });
            }
            elseif ($type === 'tags') {
                $cleanKeyword = ltrim($keyword, '#');
                $query->whereRaw('LOWER(tags) like ?', ['%' . mb_strtolower($cleanKeyword) . '%']);
            }
            elseif ($type === 'author') {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            }
        }

        $paginator = $query->paginate(10)->appends($request->query());

        $posts = $paginator->getCollection()->map(function ($post) {
            return [
            'id' => $post->id,
            'authorName' => $post->user->name ?? '탈퇴한 사용자',
            'authorAvatar' => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
            'timeAgo' => $post->created_at->diffForHumans(),
            'category' => $post->category->name ?? '일반',
            'tags' => $post->tags ?? [],
            'title' => $post->title,
            'summary' => $post->summary,
            'likes' => $post->likes_count ?? 0,
            'views' => $post->view_count,
            ];
        });

        $paginatedData = [
            'data' => $posts,
            'next_page_url' => $paginator->nextPageUrl(),
        ];

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json($paginatedData);
        }

        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();

        return \Inertia\Inertia::render('Home', [
            'posts' => $paginatedData,
            'categories' => $categories,
            'currentCategory' => $request->category ?? 'all',
            'filters' => $request->only(['search_type', 'search_keyword'])
        ]);
    }

    public function popular()
    {
        $posts = \App\Models\Post::with(['user', 'category'])->orderBy('view_count', 'desc')->get()->map(function ($post) {
            return [
            'id' => $post->id,
            'authorName' => $post->user->name ?? '탈퇴한 사용자',
            'authorAvatar' => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? '?') . '&background=random',
            'timeAgo' => $post->created_at->diffForHumans(),
            'category' => $post->category->name ?? '일반',
            'tags' => $post->tags ?? [],
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
