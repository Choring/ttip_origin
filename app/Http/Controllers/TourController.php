<?php

namespace App\Http\Controllers;

use App\Models\TouristSpot;
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

        return Inertia::render('Tour/Show', [
            'spot' => [
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
                'overview'      => $spot->overview,
                'source'        => $spot->source,
            ],
            'relatedSpots' => $related,
        ]);
    }
}
