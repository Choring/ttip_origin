<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class ImageController extends Controller
{
    /**
     * Handle image upload from Tiptap editor with automatic compression and resizing.
     * GD → Imagick → 원본 저장 순서로 폴백합니다.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image uploaded'], 400);
        }

        try {
            $file      = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time();
            $disk      = config('filesystems.default');

            // GIF: 애니메이션 보존을 위해 압축 없이 원본 저장
            if ($extension === 'gif') {
                $path = $file->storeAs('content', $fileName . '.gif', $disk);

                return response()->json([
                    'url' => Storage::disk($disk)->url($path),
                ]);
            }

            // GD / Imagick 중 사용 가능한 드라이버로 WebP 변환 시도
            $driver = $this->resolveDriver();

            if ($driver !== null) {
                $manager = new ImageManager($driver);
                $image   = $manager->read($file);
                $image->scaleDown(1920, 1920);
                $encoded    = $image->toWebp(80);
                $newFileName = $fileName . '.webp';
                $path        = 'content/' . $newFileName;
                Storage::disk($disk)->put($path, (string) $encoded);
            } else {
                // 드라이버 없음 → 원본 확장자 그대로 저장
                $path = $file->storeAs('content', $fileName . '.' . $extension, $disk);
            }

            if (!$path) {
                return response()->json(['error' => '파일을 저장소에 저장하지 못했습니다.'], 500);
            }

            return response()->json([
                'url' => Storage::disk($disk)->url($path),
            ]);

        } catch (\Exception $e) {
            \Log::error('이미지 업로드 오류: ' . $e->getMessage());
            return response()->json(['error' => '업로드 중 서버 오류가 발생했습니다.'], 500);
        }
    }

    /**
     * GD → Imagick 순서로 사용 가능한 Intervention Image 드라이버를 반환합니다.
     * 둘 다 없으면 null을 반환합니다.
     */
    private function resolveDriver(): ?object
    {
        if (extension_loaded('gd')) {
            return new GdDriver();
        }

        if (extension_loaded('imagick')) {
            return new ImagickDriver();
        }

        return null;
    }
}
