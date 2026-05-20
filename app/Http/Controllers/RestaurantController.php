<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\TouristSpot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class RestaurantController extends Controller
{
    private string $baseUrl = 'https://apis.data.go.kr/B551011/KorService2';

    public function index()
    {
        $restaurants = Restaurant::select(
                'content_id', 'title', 'category', 'address', 'image', 'homepage', 'tel'
            )
            ->orderBy('title')
            ->get();

        return Inertia::render('Restaurants/Index', [
            'restaurants' => $restaurants,
        ]);
    }

    public function show(string $contentId)
    {
        $restaurant = Restaurant::select(
                'content_id', 'title', 'category', 'address', 'image', 'homepage', 'tel', 'map_x', 'map_y'
            )->findOrFail($contentId);

        // TourAPI에서 추가 상세 정보 가져오기 (24시간 캐시)
        $detail = Cache::remember("restaurant_detail_{$contentId}", 86400, function () use ($contentId) {
            return $this->fetchDetail($contentId);
        });

        // 근처 관광지 (같은 구/군)
        $district = $this->extractDistrict($restaurant->address);
        $nearbySpots = TouristSpot::select('content_id', 'content_type_id', 'title', 'addr1', 'image', 'thumbnail')
            ->when($district, fn($q) => $q->where('addr1', 'like', "%{$district}%"))
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->map(fn($s) => [
                'contentId'     => $s->content_id,
                'contentTypeId' => $s->content_type_id,
                'title'         => $s->title,
                'addr1'         => $s->addr1,
                'image'         => $s->image ?? $s->thumbnail,
            ]);

        // 연관 맛집 (같은 카테고리 우선, 랜덤 3개)
        $related = Restaurant::where('content_id', '!=', $contentId)
            ->where('category', $restaurant->category)
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->map(fn($r) => [
                'contentId' => $r->content_id,
                'title'     => $r->title,
                'category'  => $r->category,
                'address'   => $r->address,
                'image'     => $r->image,
            ]);

        return Inertia::render('Restaurants/Show', [
            'restaurant' => [
                'contentId'   => $restaurant->content_id,
                'title'       => $restaurant->title,
                'category'    => $restaurant->category,
                'address'     => $restaurant->address,
                'image'       => $restaurant->image,
                'homepage'    => $restaurant->homepage,
                'tel'         => $restaurant->tel,
                // 좌표 (DB에서 직접)
                'mapX'        => $restaurant->map_x,
                'mapY'        => $restaurant->map_y,
                // TourAPI 추가 정보
                'overview'    => $detail['overview']    ?? null,
                'firstmenu'   => $detail['firstmenu']   ?? null,
                'opentimefood'=> $detail['opentimefood'] ?? null,
                'restdatefood'=> $detail['restdatefood'] ?? null,
                'parkingfood' => $detail['parkingfood']  ?? null,
                'seat'        => $detail['seat']         ?? null,
                'smoking'     => $detail['smoking']      ?? null,
                'chkcreditcardfood' => $detail['chkcreditcardfood'] ?? null,
                'extraImages' => $detail['extraImages']  ?? [],
            ],
            'relatedRestaurants' => $related,
            'nearbySpots'        => $nearbySpots,
        ]);
    }

    private function extractDistrict(?string $address): ?string
    {
        if (!$address) return null;
        preg_match('/(\S+구|\S+군)/', $address, $matches);
        return $matches[1] ?? null;
    }

    private function fetchDetail(string $contentId): array
    {
        $apiKey = urlencode(env('TOUR_API_KEY', ''));
        $result = [];

        // 공통 정보 (overview, homepage, mapX, mapY)
        try {
            $res = Http::timeout(10)->withoutVerifying()->get("{$this->baseUrl}/detailCommon2", [
                'serviceKey'    => urldecode($apiKey),
                'contentId'     => $contentId,
                'contentTypeId' => 39,
                'defaultYN'     => 'Y',
                'overviewYN'    => 'Y',
                'MobileOS'      => 'ETC',
                'MobileApp'     => 'ttip',
                '_type'         => 'json',
            ]);
            $item = data_get($res->json(), 'response.body.items.item.0') ?? data_get($res->json(), 'response.body.items.item');
            if ($item) {
                $result['overview'] = $item['overview'] ?? null;
            }
        } catch (\Exception $e) {}

        // 음식점 상세 소개 정보
        try {
            $res = Http::timeout(10)->withoutVerifying()->get("{$this->baseUrl}/detailIntro2", [
                'serviceKey'    => urldecode($apiKey),
                'contentId'     => $contentId,
                'contentTypeId' => 39,
                'MobileOS'      => 'ETC',
                'MobileApp'     => 'ttip',
                '_type'         => 'json',
            ]);
            $item = data_get($res->json(), 'response.body.items.item.0') ?? data_get($res->json(), 'response.body.items.item');
            if ($item) {
                $result['firstmenu']        = $item['firstmenu']        ?? null;
                $result['opentimefood']     = $item['opentimefood']     ?? null;
                $result['restdatefood']     = $item['restdatefood']     ?? null;
                $result['parkingfood']      = $item['parkingfood']      ?? null;
                $result['seat']             = $item['seat']             ?? null;
                $result['smoking']          = $item['smoking']          ?? null;
                $result['chkcreditcardfood']= $item['chkcreditcardfood'] ?? null;
            }
        } catch (\Exception $e) {}

        // 추가 이미지
        try {
            $res = Http::timeout(10)->withoutVerifying()->get("{$this->baseUrl}/detailImage2", [
                'serviceKey'    => urldecode($apiKey),
                'contentId'     => $contentId,
                'imageYN'       => 'Y',
                'subImageYN'    => 'Y',
                'MobileOS'      => 'ETC',
                'MobileApp'     => 'ttip',
                '_type'         => 'json',
            ]);
            $items = data_get($res->json(), 'response.body.items.item', []);
            if (isset($items['originimgurl'])) $items = [$items];
            $result['extraImages'] = collect($items)->pluck('originimgurl')->filter()->values()->toArray();
        } catch (\Exception $e) {
            $result['extraImages'] = [];
        }

        return $result;
    }
}
