<?php

namespace App\Console\Commands;

use App\Models\TouristSpot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchTouristSpots extends Command
{
    protected $signature = 'app:fetch-tourist-spots';
    protected $description = '한국관광공사 API에서 대구 관광지 정보를 수집하여 DB에 저장';

    private string $baseUrl = 'https://apis.data.go.kr/B551011/KorService2';

    public function handle()
    {
        $this->info('대구 관광지 데이터 수집 시작...');

        $apiKey = urlencode(env('TOUR_API_KEY', ''));
        if (!$apiKey) {
            $this->error('TOUR_API_KEY가 설정되지 않았습니다.');
            return;
        }

        try {
            // 1단계: 목록 전체 수집 (페이지 순회)
            $spots = $this->fetchAllSpots($apiKey);
            $this->info("목록 수집 완료: {$spots->count()}건");

            // 2단계: 각 관광지 overview(상세소개) 수집 후 DB 저장
            $count = 0;
            foreach ($spots as $item) {
                $contentId = $item['contentid'] ?? null;
                if (!$contentId) continue;

                $overview = $this->fetchOverview($apiKey, $contentId);

                // source = 'manual'인 항목은 덮어쓰지 않음
                $existing = TouristSpot::find($contentId);
                if ($existing && $existing->source === 'manual') {
                    continue;
                }

                TouristSpot::updateOrCreate(
                    ['content_id' => $contentId],
                    [
                        'title'           => $item['title'] ?? '',
                        'addr1'           => $item['addr1'] ?? '',
                        'addr2'           => $item['addr2'] ?? '',
                        'image'           => $item['firstimage'] ?? '',
                        'thumbnail'       => $item['firstimage2'] ?? '',
                        'map_x'           => $item['mapx'] ?? '',
                        'map_y'           => $item['mapy'] ?? '',
                        'tel'             => $item['tel'] ?? '',
                        'overview'        => $overview,
                        'content_type_id' => $item['contenttypeid'] ?? '12',
                        'source'          => 'api',
                        'fetched_at'      => now(),
                    ]
                );
                $count++;
            }

            $this->info("DB 저장 완료: {$count}건");

        } catch (\Exception $e) {
            $this->error('오류 발생: ' . $e->getMessage());
            Log::error('FetchTouristSpots Error: ' . $e->getMessage());
        }
    }

    private function fetchAllSpots($apiKey): \Illuminate\Support\Collection
    {
        $spots = collect();
        $page  = 1;

        do {
            $query = http_build_query([
                'MobileOS'      => 'ETC',
                'MobileApp'     => 'ttip',
                '_type'         => 'json',
                'areaCode'      => '4',
                'contentTypeId' => '12',
                'arrange'       => 'A',
                'numOfRows'     => '100',
                'pageNo'        => (string) $page,
            ]);

            $url      = "{$this->baseUrl}/areaBasedList2?serviceKey={$apiKey}&{$query}";
            $response = Http::withoutVerifying()->timeout(30)->get($url);

            if (!$response->successful()) break;

            $items = data_get($response->json(), 'response.body.items.item', []);
            if (!is_array($items) || empty($items)) break;

            $spots = $spots->merge($items);

            $total    = (int) data_get($response->json(), 'response.body.totalCount', 0);
            $fetched  = $spots->count();
            $page++;

        } while ($fetched < $total);

        return $spots;
    }

    private function fetchOverview($apiKey, $contentId): string
    {
        $query = http_build_query([
            'MobileOS'     => 'ETC',
            'MobileApp'    => 'ttip',
            '_type'        => 'json',
            'contentId'    => $contentId,
            'defaultYN'    => 'Y',
            'firstImageYN' => 'Y',
            'addrinfoYN'   => 'Y',
            'mapinfoYN'    => 'Y',
            'overviewYN'   => 'Y',
        ]);

        $url      = "{$this->baseUrl}/detailCommon2?serviceKey={$apiKey}&{$query}";
        $response = Http::withoutVerifying()->timeout(10)->get($url);

        if (!$response->successful()) return '';

        $item = data_get($response->json(), 'response.body.items.item.0', null);
        if (!$item) return '';

        return strip_tags($item['overview'] ?? '');
    }
}
