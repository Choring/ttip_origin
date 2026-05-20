<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::orderBy('title');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title',   'like', "%{$request->search}%")
                  ->orWhere('address', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $restaurants = $query->paginate(20)->appends($request->query());

        return Inertia::render('Admin/Restaurant/Index', [
            'restaurants' => $restaurants,
            'filters'     => $request->only(['search', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Restaurant/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content_id'       => 'required|string|unique:restaurants,content_id',
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|max:50',
            'address'          => 'nullable|string|max:255',
            'image'            => 'nullable|image|max:10240',
            'new_extra_images'   => 'nullable|array',
            'new_extra_images.*' => 'image|max:10240',
            'homepage'         => 'nullable|max:1000',
            'tel'              => 'nullable|string|max:50',
            'map_x'            => 'nullable|string',
            'map_y'            => 'nullable|string',
        ]);

        $data = $request->only([
            'content_id', 'title', 'category', 'address',
            'homepage', 'tel', 'map_x', 'map_y',
        ]);

        $disk = config('filesystems.default');

        if ($request->hasFile('image')) {
            $path = FileUploadHelper::upload($request->file('image'), 'restaurants');
            $data['image'] = Storage::disk($disk)->url($path);
        }

        $extraImages = [];
        if ($request->hasFile('new_extra_images')) {
            foreach ($request->file('new_extra_images') as $file) {
                $path = FileUploadHelper::upload($file, 'restaurants');
                $extraImages[] = Storage::disk($disk)->url($path);
            }
        }
        $data['extra_images'] = !empty($extraImages) ? $extraImages : null;

        Restaurant::create($data);

        return redirect()->route('admin.restaurants.index')
            ->with('success', '맛집이 등록되었습니다.');
    }

    public function edit(Restaurant $restaurant)
    {
        return Inertia::render('Admin/Restaurant/Edit', [
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'title'                   => 'required|string|max:255',
            'category'                => 'required|string|max:50',
            'address'                 => 'nullable|string|max:255',
            'image'                   => 'nullable|image|max:10240',
            'existing_extra_images'   => 'nullable|array',
            'existing_extra_images.*' => 'nullable|string',
            'new_extra_images'        => 'nullable|array',
            'new_extra_images.*'      => 'image|max:10240',
            'homepage'                => 'nullable|max:1000',
            'tel'                     => 'nullable|string|max:50',
            'map_x'                   => 'nullable|string',
            'map_y'                   => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'category', 'address',
            'homepage', 'tel', 'map_x', 'map_y',
        ]);

        $disk = config('filesystems.default');

        if ($request->hasFile('image')) {
            $this->deleteStorageFile($restaurant->image);
            $path = FileUploadHelper::upload($request->file('image'), 'restaurants');
            $data['image'] = Storage::disk($disk)->url($path);
        }

        // 기존 유지 이미지 + 새 업로드 이미지 병합
        $kept = array_values(array_filter($request->input('existing_extra_images', [])));

        $removed = array_diff($restaurant->extra_images ?? [], $kept);
        foreach ($removed as $url) {
            $this->deleteStorageFile($url);
        }

        $newlyUploaded = [];
        if ($request->hasFile('new_extra_images')) {
            foreach ($request->file('new_extra_images') as $file) {
                $path = FileUploadHelper::upload($file, 'restaurants');
                $newlyUploaded[] = Storage::disk($disk)->url($path);
            }
        }

        $merged = array_values(array_merge($kept, $newlyUploaded));
        $data['extra_images'] = !empty($merged) ? $merged : null;

        $restaurant->update($data);

        return redirect()->route('admin.restaurants.index')
            ->with('success', '맛집 정보가 수정되었습니다.');
    }

    public function destroy(Restaurant $restaurant)
    {
        $this->deleteStorageFile($restaurant->image);
        foreach ($restaurant->extra_images ?? [] as $url) {
            $this->deleteStorageFile($url);
        }
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')
            ->with('success', '맛집이 삭제되었습니다.');
    }

    private function deleteStorageFile(?string $url): void
    {
        if (!$url) return;
        try {
            $disk    = config('filesystems.default');
            $diskUrl = Storage::disk($disk)->url('');
            if (!str_starts_with($url, $diskUrl)) return;
            $path = ltrim(str_replace($diskUrl, '', $url), '/');
            Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {}
    }
}
