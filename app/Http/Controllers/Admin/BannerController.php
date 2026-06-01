<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        $banners = MainBanner::orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('Admin/Banner/Index', [
            'banners' => $banners,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Banner/Form', [
            'banner' => null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:100',
            'subtitle'   => 'nullable|string|max:100',
            'image'      => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'link_url'   => 'nullable|string|max:500',
            'sort_order' => 'integer|min:0',
            'started_at' => 'required|date',
            'ended_at'   => 'required|date|after_or_equal:started_at',
        ]);

        $imageUrl = $this->uploadImage($request);

        MainBanner::create([
            'title'      => $request->title,
            'subtitle'   => $request->subtitle,
            'image_url'  => $imageUrl,
            'link_url'   => $request->link_url,
            'is_active'  => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
            'started_at' => $request->started_at,
            'ended_at'   => $request->ended_at,
        ]);

        return redirect()->route('admin.banners.index')->with('success', '배너가 등록되었습니다.');
    }

    public function edit(MainBanner $banner)
    {
        return Inertia::render('Admin/Banner/Form', [
            'banner' => $banner,
        ]);
    }

    public function update(Request $request, MainBanner $banner)
    {
        $request->validate([
            'title'      => 'required|string|max:100',
            'subtitle'   => 'nullable|string|max:100',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'link_url'   => 'nullable|string|max:500',
            'sort_order' => 'integer|min:0',
            'started_at' => 'required|date',
            'ended_at'   => 'required|date|after_or_equal:started_at',
        ]);

        // 새 이미지 업로드 시 기존 파일 삭제
        $imageUrl = $banner->image_url;
        if ($request->hasFile('image')) {
            $this->deleteOldImage($banner->image_url);
            $imageUrl = $this->uploadImage($request);
        }

        $banner->update([
            'title'      => $request->title,
            'subtitle'   => $request->subtitle,
            'image_url'  => $imageUrl,
            'link_url'   => $request->link_url,
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'started_at' => $request->started_at,
            'ended_at'   => $request->ended_at,
        ]);

        return redirect()->route('admin.banners.index')->with('success', '배너가 수정되었습니다.');
    }

    public function destroy(MainBanner $banner)
    {
        $this->deleteOldImage($banner->image_url);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', '배너가 삭제되었습니다.');
    }

    public function toggleActive(MainBanner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return back()->with('success', $banner->is_active ? '배너가 활성화되었습니다.' : '배너가 비활성화되었습니다.');
    }

    // ── 이미지 업로드 헬퍼 ──────────────────────────────────────────────────
    private function uploadImage(Request $request): string
    {
        $disk = config('filesystems.default');
        $path = $request->file('image')->store('banners', $disk);
        return Storage::disk($disk)->url($path);
    }

    private function deleteOldImage(?string $imageUrl): void
    {
        if (!$imageUrl) return;
        $disk = config('filesystems.default');
        // URL에서 경로 추출
        $path = parse_url($imageUrl, PHP_URL_PATH);
        $path = ltrim($path, '/');
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
