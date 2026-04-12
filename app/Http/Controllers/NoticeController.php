<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeController extends Controller
{
    /**
     * Display a listing of notices.
     */
    public function index()
    {
        $posts = Post::with(['user', 'category'])->withCount(['comments', 'likes'])
            ->notice()
            ->latest()
            ->paginate(15)
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'authorName' => $post->user->name ?? '운영자',
                    'authorAvatar' => 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? 'A') . '&background=random',
                    'timeAgo' => $post->created_at->diffForHumans(),
                    'category' => $post->category->name ?? '공지',
                    'tags' => $post->tags ?? [],
                    'title' => $post->title,
                    'summary' => $post->summary,
                    'is_pinned' => $post->is_pinned,
                    'views' => $post->view_count,
                    'likes' => $post->likes_count ?? 0,
                    'comments' => $post->comments_count ?? 0,
                ];
            });

        return Inertia::render('Notice/Index', [
            'posts' => $posts
        ]);
    }
}
