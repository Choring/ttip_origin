<?php

namespace App\Http\Middleware;

use App\Models\Inquiry;
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
                    ? array_merge(
                        $request->user()->loadMissing('tier')->only([
                            'id', 'name', 'email', 'role',
                            'current_points', 'tier_id', 'tier',
                        ]),
                        ['unread_notifications_count' => $request->user()->unreadNotifications->count()]
                    )
                    : null,
            ],
            'flash' => [
                'success'      => session('success'),
                'error'        => session('error'),
                'point_gain'   => session('point_gain'),
                'tier_upgrade' => session('tier_upgrade'), // ['name' => '새싹', 'icon' => '🌿']
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
            // 어드민 사이드바 미답변 문의 수
            'pendingInquiryCount' => $request->user()?->role === 'admin'
                ? \Illuminate\Support\Facades\Cache::remember('pending_inquiry_count', 60, fn() => Inquiry::where('status', 'pending')->count())
                : 0,
            // 어드민 사이드바 미처리 신고 수
            'pendingReportCount' => $request->user()?->role === 'admin'
                ? \Illuminate\Support\Facades\Cache::remember('pending_report_count', 60, fn() => \App\Models\Report::where('status', 'pending')->count())
                : 0,
        ];
    }
}
