<?php

namespace App\Console\Commands;

use App\Models\CulturalEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCulturalEvents extends Command
{
    protected $signature = 'app:fetch-cultural-events';
    protected $description = '한국관광공사 API에서 대구 축제/공연/행사 정보를 수집하여 DB에 저장';

    private string $baseUrl = 'https://apis.data.go.kr/B551011/KorService2';

    public function handle()
    {
        $this->info('대구 공연/전시/축제 데이터 수집 시작...');

        $apiKey = urlencode(env('TOUR_API_KEY', ''));
        if (!$apiKey) {
            $this->error('TOUR_API_KEY가 설정되지 않았습니다.');
            return 1;
        }

        $page    = 1;
        $perPage = 100;
        $total   = 0;

        do {
            $query = http_build_query([
                'numOfRows'      => $perPage,
                'pageNo'         => $page,
                'MobileOS'       => 'ETC',
                'MobileApp'      => 'ttip',
                'areaCode'       => 4,        // 대구
                
                'eventStartDate' => '20250101',
                '_type'          => 'json',
            ]);

            $url      = "{$this->baseUrl}/searchFestival2?serviceKey={$apiKey}&{$query}";
            $response = Http::timeout(30)->withoutVerifying()->get($url);

            if (!$response->successful()) {
                $this->error("API 요청 실패 (page {$page}): " . $response->status());
                break;
            }

            $body  = $response->json();
            $items = data_get($body, 'response.body.items.item', []);

            if (empty($items)) break;

            // 단일 아이템이면 배열로 감싸기
            if (isset($items['contentid'])) $items = [$items];

            foreach ($items as $item) {
                $contentId = (string) ($item['contentid'] ?? '');
                if (!$contentId) continue;

                // 날짜 포맷 변환: 20260520 → 2026-05-20
                $startDate = $this->parseDate($item['eventstartdate'] ?? '');
                $endDate   = $this->parseDate($item['eventenddate']   ?? '');

                if (!$startDate || !$endDate) continue;

                // event_gubun 추론
                $title  = $item['title'] ?? '';
                $gubun  = $this->guessGubun($title, $item['cat2'] ?? '', $item['cat3'] ?? '');

                CulturalEvent::updateOrCreate(
                    ['event_seq' => $contentId],
                    [
                        'subject'      => $title,
                        'event_gubun'  => $gubun,
                        'start_date'   => $startDate,
                        'end_date'     => $endDate,
                        'place'        => $item['addr1'] ?? null,
                        'pay'          => null,
                        'content'      => $item['overview'] ?? null,
                        'homepage'     => null,
                        'image'        => $item['firstimage'] ?? $item['firstimage2'] ?? null,
                    ]
                );
                $total++;
            }

            $this->info("  Page {$page}: " . count($items) . "건 처리");

            $totalCount = (int) data_get($body, 'response.body.totalCount', 0);
            $page++;

        } while (($page - 1) * $perPage < $totalCount);

        $this->info("완료! 총 {$total}건 저장/업데이트");
        return 0;
    }

    private function parseDate(string $raw): ?string
    {
        $raw = preg_replace('/\D/', '', $raw);
        if (strlen($raw) !== 8) return null;
        return substr($raw, 0, 4) . '-' . substr($raw, 4, 2) . '-' . substr($raw, 6, 2);
    }

    private function guessGubun(string $title, string $cat2, string $cat3): string
    {
        $cat = strtolower($cat2 . $cat3 . $title);
        if (str_contains($cat, 'A0207') || str_contains($title, '전시') || str_contains($title, '미술') || str_contains($title, '박물')) return '전시';
        if (str_contains($title, '공연') || str_contains($title, '콘서트') || str_contains($title, '뮤지컬') || str_contains($title, '연주')) return '공연';
        if (str_contains($title, '축제') || str_contains($title, '페스티벌') || str_contains($title, '마켓')) return '축제';
        return '행사';
    }
}
