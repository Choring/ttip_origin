<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class TourController extends Controller
{
    private string $apiKey;
    private string $baseUrl = 'https://apis.data.go.kr/B551011/KorService2';

    public function __construct()
    {
        $this->apiKey = urlencode(env('TOUR_API_KEY', ''));
    }

    public function index()
    {
        $spots = Cache::remember('tour_spots_daegu', 3600, function () {
            $query = http_build_query([
                'MobileOS'      => 'ETC',
                'MobileApp'     => 'ttip',
                '_type'         => 'json',
                'areaCode'      => '4',    // 대구광역시
                'contentTypeId' => '12',   // 관광지
                'arrange'       => 'A',
                'numOfRows'     => '50',
                'pageNo'        => '1',
            ]);
            $url = "{$this->baseUrl}/areaBasedList2?serviceKey={$this->apiKey}&{$query}";
            $response = Http::withoutVerifying()->get($url);

            if (!$response->successful()) {
                return [];
            }

            $items = data_get($response->json(), 'response.body.items.item', []);
            if (!is_array($items)) return [];

            return collect($items)->map(fn($item) => [
                'contentId'     => $item['contentid'] ?? '',
                'contentTypeId' => $item['contenttypeid'] ?? '12',
                'title'         => $item['title'] ?? '',
                'addr1'         => $item['addr1'] ?? '',
                'addr2'         => $item['addr2'] ?? '',
                'image'         => $item['firstimage'] ?? '',
                'thumbnail'     => $item['firstimage2'] ?? '',
                'mapX'          => $item['mapx'] ?? '',
                'mapY'          => $item['mapy'] ?? '',
                'tel'           => $item['tel'] ?? '',
            ])->values()->toArray();
        });

        return Inertia::render('Tour/Index', [
            'spots' => $spots,
        ]);
    }

    public function show(string $contentId)
    {
        $spot = Cache::remember("tour_spot_{$contentId}", 3600, function () use ($contentId) {
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
            $url = "{$this->baseUrl}/detailCommon2?serviceKey={$this->apiKey}&{$query}";
            $response = Http::withoutVerifying()->get($url);

            if (!$response->successful()) {
                return null;
            }

            $item = data_get($response->json(), 'response.body.items.item.0', null);
            if (!$item) return null;

            return [
                'contentId'     => $item['contentid'] ?? $contentId,
                'contentTypeId' => $item['contenttypeid'] ?? '12',
                'title'         => $item['title'] ?? '',
                'addr1'         => $item['addr1'] ?? '',
                'addr2'         => $item['addr2'] ?? '',
                'image'         => $item['firstimage'] ?? '',
                'thumbnail'     => $item['firstimage2'] ?? '',
                'mapX'          => $item['mapx'] ?? '',
                'mapY'          => $item['mapy'] ?? '',
                'tel'           => $item['tel'] ?? '',
                'overview'      => $item['overview'] ?? '',
            ];
        });

        if (!$spot) {
            $allSpots = Cache::get('tour_spots_daegu', []);
            $spot = collect($allSpots)->firstWhere('contentId', $contentId);
            if (!$spot) abort(404);
        }

        $allSpots = Cache::get('tour_spots_daegu', []);
        $related = collect($allSpots)
            ->filter(fn($s) => $s['contentId'] !== $contentId)
            ->take(3)
            ->values()
            ->toArray();

        return Inertia::render('Tour/Show', [
            'spot'         => $spot,
            'relatedSpots' => $related,
        ]);
    }
}
