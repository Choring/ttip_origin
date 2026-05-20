<?php

namespace App\Http\Controllers;

use App\Models\TouristSpot;
use App\Models\Restaurant;
use Inertia\Inertia;

class TourController extends Controller
{
    public function index()
    {
        $spots = TouristSpot::orderBy('title')
            ->get()
            ->map(fn($spot) => [
                'contentId'     => $spot->content_id,
                'contentTypeId' => $spot->content_type_id,
                'title'         => $spot->title,
                'addr1'         => $spot->addr1,
                'addr2'         => $spot->addr2,
                'image'         => $spot->image,
                'thumbnail'     => $spot->thumbnail,
                'mapX'          => $spot->map_x,
                'mapY'          => $spot->map_y,
                'tel'           => $spot->tel,
                'source'        => $spot->source,
            ]);

        return Inertia::render('Tour/Index', [
            'spots' => $spots,
        ]);
    }

    public function show(string $contentId)
    {
        $spot = TouristSpot::findOrFail($contentId);

        $related = TouristSpot::where('content_id', '!=', $contentId)
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->map(fn($s) => [
                'contentId'     => $s->content_id,
                'contentTypeId' => $s->content_type_id,
                'title'         => $s->title,
                'addr1'         => $s->addr1,
                'image'         => $s->image,
                'thumbnail'     => $s->thumbnail,
            ]);

        // 근처 맛집 (같은 구/군)
        $district = $this->extractDistrict($spot->addr1);
        $nearbyRestaurants = Restaurant::select('content_id', 'title', 'category', 'address', 'image')
            ->when($district, fn($q) => $q->where('address', 'like', "%{$district}%"))
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

        return Inertia::render('Tour/Show', [
            'spot' => [
                'contentId'      => $spot->content_id,
                'contentTypeId'  => $spot->content_type_id,
                'title'          => $spot->title,
                'addr1'          => $spot->addr1,
                'addr2'          => $spot->addr2,
                'image'          => $spot->image,
                'thumbnail'      => $spot->thumbnail,
                'mapX'           => $spot->map_x,
                'mapY'           => $spot->map_y,
                'tel'            => $spot->tel,
                'overview'       => $spot->overview,
                'usetime'        => $spot->usetime,
                'restdate'       => $spot->restdate,
                'usefee'         => $spot->usefee,
                'parking'        => $spot->parking,
                'chkpet'         => $spot->chkpet,
                'chkbabycarriage'=> $spot->chkbabycarriage,
                'extraImages'    => $spot->extra_images ?? [],
                'source'         => $spot->source,
            ],
            'relatedSpots'      => $related,
            'nearbyRestaurants' => $nearbyRestaurants,
        ]);
    }

    private function extractDistrict(?string $address): ?string
    {
        if (!$address) return null;
        preg_match('/(\S+구|\S+군)/', $address, $matches);
        return $matches[1] ?? null;
    }
}
