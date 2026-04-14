<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * 사이트의 주요 통계를 출력하는 관리자용 대시보드 뷰
     */
    public function index()
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // 1. 핵심 지표 (오늘 vs 어제 비교 포함)
        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'new_users_yesterday' => User::whereDate('created_at', $yesterday)->count(),
            'new_posts_today' => Post::whereDate('created_at', $today)->count(),
            'new_posts_yesterday' => Post::whereDate('created_at', $yesterday)->count(),
            'today_visitors' => \App\Models\DailyStatistic::where('date', $today)->value('visitor_count') ?? 0,
        ];

        // 2. 주간/월간 그래프 데이터 (최근 30일)
        $chartData = \App\Models\DailyStatistic::where('date', '>=', now()->subDays(30)->toDateString())
            ->orderBy('date', 'asc')
            ->get();

        $chart = [
            'labels' => $chartData->pluck('date')->map(fn($d) => date('m/d', strtotime($d))),
            'visitors' => $chartData->pluck('visitor_count'),
            'posts' => $chartData->pluck('new_posts_count'),
            'users' => $chartData->pluck('new_users_count'),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'chart' => $chart
        ]);
    }
}
