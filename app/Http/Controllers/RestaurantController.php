<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\TouristSpot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        // API 실패 시 빈 배열로 fallback — 페이지는 정상 렌더링
        try {
            $detail = Cache::remember("restaurant_detail_{$contentId}", 86400, function () use ($contentId) {
                return $this->fetchDetail($contentId);
            });
        } catch (\Exception $e) {
            Log::warning("맛집 상세 API 캐시 오류 (contentId: {$contentId}): " . $e->getMessage());
            $detail = [];
        }

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
                // DB 추가 이미지 + TourAPI 추가 이미지 합치기
                'extraImages' => array_values(array_unique(array_merge(
                    $restaurant->extra_images ?? [],
                    $detail['extraImages'] ?? []
                ))),
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
        $result = ['extraImages' => []];

        // 공통 정보 (overview)
        try {
            $res = Http::connectTimeout(3)->timeout(5)->withoutVerifying()
                ->get("{$this->baseUrl}/detailCommon2", [
                    'serviceKey'    => urldecode($apiKey),
                    'contentId'     => $contentId,
                    'contentTypeId' => 39,
                    'defaultYN'     => 'Y',
                    'overviewYN'    => 'Y',
                    'MobileOS'      => 'ETC',
                    'MobileApp'     => 'ttip',
                    '_type'         => 'json',
                ]);
            $item = data_get($res->json(), 'response.body.items.item.0')
                 ?? data_get($res->json(), 'response.body.items.item');
            if (is_array($item)) {
                $result['overview'] = $item['overview'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning("TourAPI detailCommon2 실패 (contentId: {$contentId}): " . $e->getMessage());
        }

        // 음식점 상세 소개 정보
        try {
            $res = Http::connectTimeout(3)->timeout(5)->withoutVerifying()
                ->get("{$this->baseUrl}/detailIntro2", [
                    'serviceKey'    => urldecode($apiKey),
                    'contentId'     => $contentId,
                    'contentTypeId' => 39,
                    'MobileOS'      => 'ETC',
                    'MobileApp'     => 'ttip',
                    '_type'         => 'json',
                ]);
            $item = data_get($res->json(), 'response.body.items.item.0')
                 ?? data_get($res->json(), 'response.body.items.item');
            if (is_array($item)) {
                $result['firstmenu']         = $item['firstmenu']         ?? null;
                $result['opentimefood']      = $item['opentimefood']      ?? null;
                $result['restdatefood']      = $item['restdatefood']      ?? null;
                $result['parkingfood']       = $item['parkingfood']       ?? null;
                $result['seat']              = $item['seat']              ?? null;
                $result['smoking']           = $item['smoking']           ?? null;
                $result['chkcreditcardfood'] = $item['chkcreditcardfood'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning("TourAPI detailIntro2 실패 (contentId: {$contentId}): " . $e->getMessage());
        }

        // 추가 이미지
        try {
            $res = Http::connectTimeout(3)->timeout(5)->withoutVerifying()
                ->get("{$this->baseUrl}/detailImage2", [
                    'serviceKey' => urldecode($apiKey),
                    'contentId'  => $contentId,
                    'imageYN'    => 'Y',
                    'subImageYN' => 'Y',
                    'MobileOS'   => 'ETC',
                    'MobileApp'  => 'ttip',
                    '_type'      => 'json',
                ]);
            $items = data_get($res->json(), 'response.body.items.item', []);
            if (is_array($items) && isset($items['originimgurl'])) $items = [$items];
            $result['extraImages'] = is_array($items)
                ? collect($items)->pluck('originimgurl')->filter()->values()->toArray()
                : [];
        } catch (\Exception $e) {
            Log::warning("TourAPI detailImage2 실패 (contentId: {$contentId}): " . $e->getMessage());
        }

        return $result;
    }
}
