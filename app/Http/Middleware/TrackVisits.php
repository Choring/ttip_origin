<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // GET 요청이고 AJAX 요청이 아닐 때만 집계 (페이지 뷰 기준)
        if ($request->isMethod('GET') && !$request->ajax()) {
            $ip = $request->ip();
            $today = now()->toDateString();

            try {
                // 오늘 이 IP로 기록이 있는지 확인하고 없으면 생성
                $log = \App\Models\VisitLog::firstOrCreate([
                    'ip_address' => $ip,
                    'visited_at' => $today,
                ], [
                    'user_id' => auth()->id(),
                ]);

                // 방금 생성되었다면 통계 테이블 업데이트
                if ($log->wasRecentlyCreated) {
                    \App\Models\DailyStatistic::updateOrCreate(
                        ['date' => $today],
                        ['visitor_count' => \Illuminate\Support\Facades\DB::raw('visitor_count + 1')]
                    );
                }
            } catch (\Exception $e) {
                // 로그 기록 중 오류가 나도 서비스에는 영향 없도록 처리
                \Illuminate\Support\Facades\Log::error('Visit tracking failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
