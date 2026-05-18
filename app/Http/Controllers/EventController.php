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
        $events = \App\Models\CulturalEvent::where('end_date', '>=', date('Y-m-d'))
            ->select(
                'event_seq', 'subject', 'event_gubun', 'start_date', 'end_date', 
                'place', 'pay', 'homepage',
                \Illuminate\Support\Facades\DB::raw('SUBSTRING(content, 1, 300) as content')
            )
            ->orderBy('start_date', 'asc') // 다가오는 행사순(또는 최신순)
            ->get();

        return Inertia::render('Events/Index', [
            'events' => $events
        ]);
    }
}
