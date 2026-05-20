<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchRestaurants extends Command
{
    protected $signature = 'app:fetch-restaurants';
    protected $description = '한국관광공사 API에서 대구 음식점 정보를 수집하여 DB에 저장';

    private string $baseUrl = 'https://apis.data.go.kr/B551011/KorService2';

    public function handle()
    {
        $this->info('대구 맛집 데이터 수집 시작...');

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
                'numOfRows'     => $perPage,
                'pageNo'        => $page,
                'MobileOS'      => 'ETC',
                'MobileApp'     => 'ttip',
                'contentTypeId' => 39,   // 음식점
                'areaCode'      => 4,    // 대구
                '_type'         => 'json',
            ]);

            $url      = "{$this->baseUrl}/areaBasedList2?serviceKey={$apiKey}&{$query}";
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

                $category = $this->resolveCategory($item['cat3'] ?? '');

                Restaurant::updateOrCreate(
                    ['content_id' => $contentId],
                    [
                        'title'    => $item['title']    ?? '',
                        'category' => $category,
                        'address'  => $item['addr1']    ?? null,
                        'image'    => $item['firstimage'] ?? $item['firstimage2'] ?? null,
                        'homepage' => $item['homepage'] ?? null,
                        'tel'      => $item['tel']      ?? null,
                        'map_x'    => $item['mapx']     ?? null,
                        'map_y'    => $item['mapy']     ?? null,
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

    /**
     * TourAPI cat3 코드 → 한글 카테고리
     */
    private function resolveCategory(string $cat3): string
    {
        return match ($cat3) {
            'A05020100' => '한식',
            'A05020200' => '양식',
            'A05020300' => '일식',
            'A05020400' => '중식',
            'A05020700' => '이색음식점',
            'A05020900' => '카페',
            default     => '기타',
        };
    }
}
