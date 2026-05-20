<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristSpot;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TouristSpotController extends Controller
{
    public function index(Request $request)
    {
        $query = TouristSpot::orderByRaw("source = 'manual' DESC")->orderBy('title');

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->source) {
            $query->where('source', $request->source);
        }

        $spots = $query->paginate(20)->appends($request->query());

        return Inertia::render('Admin/TouristSpot/Index', [
            'spots'   => $spots,
            'filters' => $request->only(['search', 'source']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/TouristSpot/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content_id' => 'required|string|unique:tourist_spots,content_id',
            'title'      => 'required|string|max:255',
            'addr1'      => 'nullable|string|max:255',
            'addr2'      => 'nullable|string|max:255',
            'image'      => 'nullable|url|max:500',
            'thumbnail'  => 'nullable|url|max:500',
            'map_x'      => 'nullable|string',
            'map_y'      => 'nullable|string',
            'tel'        => 'nullable|string|max:50',
            'overview'   => 'nullable|string',
        ]);

        TouristSpot::create([
            ...$validated,
            'source'     => 'manual',
            'fetched_at' => null,
        ]);

        return redirect()->route('admin.tourist-spots.index')
            ->with('success', '관광지가 성공적으로 등록되었습니다.');
    }

    public function edit(TouristSpot $touristSpot)
    {
        return Inertia::render('Admin/TouristSpot/Edit', [
            'spot' => $touristSpot,
        ]);
    }

    public function update(Request $request, TouristSpot $touristSpot)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'addr1'     => 'nullable|string|max:255',
            'addr2'     => 'nullable|string|max:255',
            'image'     => 'nullable|url|max:500',
            'thumbnail' => 'nullable|url|max:500',
            'map_x'     => 'nullable|string',
            'map_y'     => 'nullable|string',
            'tel'       => 'nullable|string|max:50',
            'overview'  => 'nullable|string',
        ]);

        $touristSpot->update($validated);

        return redirect()->route('admin.tourist-spots.index')
            ->with('success', '관광지 정보가 수정되었습니다.');
    }

    public function destroy(TouristSpot $touristSpot)
    {
        $touristSpot->delete();

        return redirect()->route('admin.tourist-spots.index')
            ->with('success', '관광지가 삭제되었습니다.');
    }
}
