<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\TouristSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $request->validate([
            'content_id'         => 'required|string|unique:tourist_spots,content_id',
            'title'              => 'required|string|max:255',
            'addr1'              => 'nullable|string|max:255',
            'addr2'              => 'nullable|string|max:255',
            'image'              => 'nullable|image|max:10240',
            'thumbnail'          => 'nullable|image|max:10240',
            'new_extra_images'   => 'nullable|array',
            'new_extra_images.*' => 'image|max:10240',
            'map_x'              => 'nullable|string',
            'map_y'              => 'nullable|string',
            'tel'                => 'nullable|string|max:50',
            'overview'           => 'nullable|string',
            'usetime'            => 'nullable|string',
            'restdate'           => 'nullable|string',
            'usefee'             => 'nullable|string',
            'parking'            => 'nullable|string',
        ]);

        $data = $request->only([
            'content_id', 'title', 'addr1', 'addr2',
            'map_x', 'map_y', 'tel', 'overview',
            'usetime', 'restdate', 'usefee', 'parking',
        ]);

        $disk = config('filesystems.default');

        // 대표 이미지
        if ($request->hasFile('image')) {
            $path = FileUploadHelper::upload($request->file('image'), 'tourist-spots');
            $data['image'] = Storage::disk($disk)->url($path);
        }

        // 썸네일
        if ($request->hasFile('thumbnail')) {
            $path = FileUploadHelper::upload($request->file('thumbnail'), 'tourist-spots');
            $data['thumbnail'] = Storage::disk($disk)->url($path);
        }

        // 추가 이미지
        $extraImages = [];
        if ($request->hasFile('new_extra_images')) {
            foreach ($request->file('new_extra_images') as $file) {
                $path = FileUploadHelper::upload($file, 'tourist-spots');
                $extraImages[] = Storage::disk($disk)->url($path);
            }
        }
        $data['extra_images'] = !empty($extraImages) ? $extraImages : null;
        $data['source']       = 'manual';
        $data['fetched_at']   = null;

        TouristSpot::create($data);

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
        $request->validate([
            'title'                    => 'required|string|max:255',
            'addr1'                    => 'nullable|string|max:255',
            'addr2'                    => 'nullable|string|max:255',
            'image'                    => 'nullable|image|max:10240',
            'thumbnail'                => 'nullable|image|max:10240',
            'existing_extra_images'    => 'nullable|array',
            'existing_extra_images.*'  => 'nullable|string',
            'new_extra_images'         => 'nullable|array',
            'new_extra_images.*'       => 'image|max:10240',
            'map_x'                    => 'nullable|string',
            'map_y'                    => 'nullable|string',
            'tel'                      => 'nullable|string|max:50',
            'overview'                 => 'nullable|string',
            'usetime'                  => 'nullable|string',
            'restdate'                 => 'nullable|string',
            'usefee'                   => 'nullable|string',
            'parking'                  => 'nullable|string',
        ]);

        $data = $request->only([
            'title', 'addr1', 'addr2',
            'map_x', 'map_y', 'tel', 'overview',
            'usetime', 'restdate', 'usefee', 'parking',
        ]);

        $disk = config('filesystems.default');

        // 대표 이미지 - 새 파일이 있으면 교체, 없으면 기존 유지
        if ($request->hasFile('image')) {
            $this->deleteStorageFile($touristSpot->image);
            $path = FileUploadHelper::upload($request->file('image'), 'tourist-spots');
            $data['image'] = Storage::disk($disk)->url($path);
        }

        // 썸네일 - 새 파일이 있으면 교체
        if ($request->hasFile('thumbnail')) {
            $this->deleteStorageFile($touristSpot->thumbnail);
            $path = FileUploadHelper::upload($request->file('thumbnail'), 'tourist-spots');
            $data['thumbnail'] = Storage::disk($disk)->url($path);
        }

        // 추가 이미지: 기존 유지 목록 + 새 업로드
        $kept = array_values(array_filter($request->input('existing_extra_images', [])));

        // 삭제된 이미지 정리
        $removed = array_diff($touristSpot->extra_images ?? [], $kept);
        foreach ($removed as $url) {
            $this->deleteStorageFile($url);
        }

        $newlyUploaded = [];
        if ($request->hasFile('new_extra_images')) {
            foreach ($request->file('new_extra_images') as $file) {
                $path = FileUploadHelper::upload($file, 'tourist-spots');
                $newlyUploaded[] = Storage::disk($disk)->url($path);
            }
        }

        $merged = array_values(array_merge($kept, $newlyUploaded));
        $data['extra_images'] = !empty($merged) ? $merged : null;

        $touristSpot->update($data);

        return redirect()->route('admin.tourist-spots.index')
            ->with('success', '관광지 정보가 수정되었습니다.');
    }

    public function destroy(TouristSpot $touristSpot)
    {
        $this->deleteStorageFile($touristSpot->image);
        $this->deleteStorageFile($touristSpot->thumbnail);
        foreach ($touristSpot->extra_images ?? [] as $url) {
            $this->deleteStorageFile($url);
        }

        $touristSpot->delete();

        return redirect()->route('admin.tourist-spots.index')
            ->with('success', '관광지가 삭제되었습니다.');
    }

    /**
     * 직접 업로드한 파일을 스토리지에서 삭제.
     * 외부 API URL(https://...)은 무시.
     */
    private function deleteStorageFile(?string $url): void
    {
        if (!$url) return;

        $disk = config('filesystems.default');

        // S3 등 외부 스토리지 URL인지 확인 후 경로 추출
        try {
            $diskUrl = Storage::disk($disk)->url('');
            if (!str_starts_with($url, $diskUrl)) return; // 다른 출처 URL은 무시

            $path = ltrim(str_replace($diskUrl, '', $url), '/');
            Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {
            // 삭제 실패해도 무시
        }
    }
}
