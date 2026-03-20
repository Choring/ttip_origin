<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $posts = \App\Models\Post::with('user')->latest()->get()->map(function($post) {
            return [
                'id' => $post->id,
                'authorName' => $post->user->name ?? '탈퇴한 사용자',
                'authorAvatar' => 'https://ui-avatars.com/api/?name='.urlencode($post->user->name ?? '?').'&background=random',
                'timeAgo' => $post->created_at->diffForHumans(),
                'category' => '일반',
                'tag' => '일반',
                'title' => $post->title,
                'summary' => $post->summary,
                'likes' => 0,
            ];
        });

        return \Inertia\Inertia::render('Home', [
            'posts' => $posts
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
