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
        // 1시간(3600초) 단위로 공공 API 응답 결과를 캐싱하여 속도와 API 부하 최소화
        $events = Cache::remember('daegu_cultural_events', 3600, function () {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying() // SSL 에러 방지 (필요에 따라 제거)
                    ->get('https://dgfca.or.kr/api/daegu/cultural-events');

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // 만약 데이터가 배열 형태가 아니라면 배열로 변환하거나 빈 배열 반환
                    return is_array($data) ? $data : [];
                }
            } catch (\Exception $e) {
                \Log::error('Event API Fetch Error: ' . $e->getMessage());
            }

            return []; // 실패 시 빈 배열 반환
        });

        return Inertia::render('Events/Index', [
            'events' => $events
        ]);
    }
}
