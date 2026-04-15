<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\DailyStatistic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * 사이트의 주요 통계를 출력하는 관리자용 대시보드 뷰
     */
    public function index()
    {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // ── 1. 핵심 지표 ──────────────────────────────────────────────────────
        $stats = [
            'total_users'         => User::count(),
            'total_posts'         => Post::count(),
            'new_users_today'     => User::whereDate('created_at', $today)->count(),
            'new_users_yesterday' => User::whereDate('created_at', $yesterday)->count(),
            'new_posts_today'     => Post::whereDate('created_at', $today)->count(),
            'new_posts_yesterday' => Post::whereDate('created_at', $yesterday)->count(),
            'today_visitors'      => DailyStatistic::where('date', $today)->value('visitor_count') ?? 0,
            'total_visitors_7'    => DailyStatistic::where('date', '>=', now()->subDays(7)->toDateString())->sum('visitor_count'),
            'total_visitors_30'   => DailyStatistic::where('date', '>=', now()->subDays(30)->toDateString())->sum('visitor_count'),
        ];

        // ── 2. 차트 데이터 빌더 (공통 함수) ─────────────────────────────────
        $buildChart = function (int $days) {
            // 날짜 범위의 모든 날짜를 미리 생성 (데이터 없는 날 = 0)
            $range = collect();
            for ($i = $days - 1; $i >= 0; $i--) {
                $range->put(now()->subDays($i)->toDateString(), [
                    'visitor_count'   => 0,
                    'new_posts_count' => 0,
                    'new_users_count' => 0,
                ]);
            }

            // DB 데이터로 채우기
            DailyStatistic::where('date', '>=', now()->subDays($days)->toDateString())
                ->orderBy('date', 'asc')
                ->get()
                ->each(function ($row) use (&$range) {
                    $range->put($row->date, [
                        'visitor_count'   => $row->visitor_count   ?? 0,
                        'new_posts_count' => $row->new_posts_count ?? 0,
                        'new_users_count' => $row->new_users_count ?? 0,
                    ]);
                });

            return [
                'labels'   => $range->keys()->map(fn($d) => date('m/d', strtotime($d)))->values(),
                'visitors' => $range->pluck('visitor_count')->values(),
                'posts'    => $range->pluck('new_posts_count')->values(),
                'users'    => $range->pluck('new_users_count')->values(),
            ];
        };

        return Inertia::render('Admin/Dashboard', [
            'stats'   => $stats,
            'chart7'  => $buildChart(7),
            'chart30' => $buildChart(30),
        ]);
    }
}
