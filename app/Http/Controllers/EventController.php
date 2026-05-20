<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        // 백그라운드 스케줄러가 수집한 자체 DB에서 데이터를 즉시 가져옴
        // 이미 종료된 행사는 제외하고, 행사 시작일 기준 최신순으로 정렬
        $oneYearAgo = now()->subYear()->toDateString();

        $events = \App\Models\CulturalEvent::select(
                'event_seq', 'subject', 'event_gubun', 'start_date', 'end_date',
                'place', 'pay', 'homepage', 'image',
                \Illuminate\Support\Facades\DB::raw('SUBSTRING(content, 1, 300) as content')
            )
            // 진행중/예정 OR 작년 이후 종료된 행사만
            ->where(function ($q) use ($oneYearAgo) {
                $q->where('end_date', '>=', now()->toDateString())       // 진행중/예정
                  ->orWhere(function ($q2) use ($oneYearAgo) {
                      $q2->where('end_date', '<', now()->toDateString()) // 종료됐지만
                         ->where('end_date', '>=', $oneYearAgo);         // 작년 이후
                  });
            })
            // 진행중/예정 먼저, 그 다음 종료된 행사 (최근 종료순)
            ->orderByRaw("CASE WHEN end_date >= CURDATE() THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN end_date >= CURDATE() THEN start_date END ASC")
            ->orderByRaw("CASE WHEN end_date < CURDATE() THEN end_date END DESC")
            ->get();

        return Inertia::render('Events/Index', [
            'events' => $events
        ]);
    }
}
