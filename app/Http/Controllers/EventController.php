<?php

namespace App\Http\Controllers;

use App\Models\CulturalEvent;
use Illuminate\Http\Request;
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

    public function show(string $eventSeq)
    {
        $event = CulturalEvent::findOrFail($eventSeq);

        // 관련 행사 (같은 분류 우선, 부족하면 다른 행사로 채움, 최대 4개)
        $mapEvent = fn($e) => [
            'eventSeq'   => $e->event_seq,
            'subject'    => $e->subject,
            'eventGubun' => $e->event_gubun,
            'startDate'  => $e->start_date,
            'endDate'    => $e->end_date,
            'place'      => $e->place,
            'image'      => $e->image,
        ];

        $baseQuery = CulturalEvent::where('event_seq', '!=', $eventSeq)
            ->where('end_date', '>=', now()->subMonths(3)->toDateString())
            ->orderByRaw("CASE WHEN end_date >= CURDATE() THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN end_date >= CURDATE() THEN start_date END ASC");

        $sameCategory = (clone $baseQuery)
            ->where('event_gubun', $event->event_gubun)
            ->limit(4)
            ->get();

        if ($sameCategory->count() < 4) {
            $excludeIds = $sameCategory->pluck('event_seq')->push($eventSeq)->toArray();
            $fill = (clone $baseQuery)
                ->whereNotIn('event_seq', $excludeIds)
                ->limit(4 - $sameCategory->count())
                ->get();
            $related = $sameCategory->concat($fill)->map($mapEvent);
        } else {
            $related = $sameCategory->map($mapEvent);
        }

        return Inertia::render('Events/Show', [
            'event' => [
                'eventSeq'   => $event->event_seq,
                'subject'    => $event->subject,
                'eventGubun' => $event->event_gubun,
                'startDate'  => $event->start_date,
                'endDate'    => $event->end_date,
                'place'      => $event->place,
                'pay'        => $event->pay,
                'content'    => $event->content,
                'homepage'   => $event->homepage,
                'image'      => $event->image,
            ],
            'relatedEvents' => $related,
        ]);
    }
}
