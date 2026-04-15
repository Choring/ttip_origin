<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()
                    ? $request->user()->loadMissing('tier')->only([
                        'id', 'name', 'email', 'role',
                        'current_points', 'tier_id', 'tier',
                    ])
                    : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'point_gain' => session('point_gain'),
            ],

            'categories' => \Illuminate\Support\Facades\Cache::remember('active_categories', 600, function () {
                return \App\Models\Category::where('is_active', true)->whereNotIn('slug', ['all', 'notice'])->orderBy('sort_order')->get();
            }),
            'popular_posts' => \Illuminate\Support\Facades\Cache::remember('popular_posts_sidebar', 60, function () {
                return \App\Models\Post::select('id', 'title', 'view_count', 'likes_count')
                    ->orderByRaw('(view_count + likes_count) DESC')
                    ->take(5)
                    ->get()
                    ->map(function ($post) {
                        return [
                            'id' => $post->id,
                            'title' => $post->title,
                            'score' => $post->view_count + $post->likes_count,
                        ];
                    });
            }),
            'hall_of_fame' => \Illuminate\Support\Facades\Cache::remember('hall_of_fame_sidebar', 60, function () {
                return \App\Models\User::select('id', 'name', 'current_points')
                    ->orderBy('current_points', 'desc')
                    ->take(3)
                    ->get();
            }),
        ];
    }
}
