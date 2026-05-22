<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadHelper
{
    /**
     * 이미지 파일을 리사이즈 + WebP 변환 후 지정 폴더에 저장합니다.
     *
     * - GIF : 애니메이션 보존을 위해 원본 그대로 저장
     * - 그 외: 최대 1200×900px 스케일다운 + WebP 85% 품질 압축
     *
     * @param  UploadedFile  $file
     * @param  string        $directory   저장 폴더 (예: 'posts')
     * @param  int           $maxWidth    최대 너비 (px)
     * @param  int           $maxHeight   최대 높이 (px)
     * @param  int           $quality     WebP 품질 (0-100)
     * @return string  저장된 파일의 상대 경로 (예: posts/aB3cDe1234_1700000000.webp)
     */
    public static function upload(
        UploadedFile $file,
        string $directory = 'uploads',
        int $maxWidth  = 1200,
        int $maxHeight = 900,
        int $quality   = 85
    ): string {
        $disk      = config('filesystems.default');
        $extension = strtolower($file->getClientOriginalExtension());
        $basename  = Str::random(10) . '_' . time();

        // GIF: 원본 그대로 저장 (애니메이션 깨짐 방지)
        if ($extension === 'gif') {
            $filename = $basename . '.gif';
            $file->storeAs($directory, $filename, $disk);
            return $directory . '/' . $filename;
        }

        // 나머지: Intervention Image로 리사이즈 + WebP 변환
        $manager = new ImageManager(new Driver());
        $image   = $manager->read($file);

        // 원본보다 작으면 확대하지 않고, 크면 비율 유지하며 축소
        $image->scaleDown($maxWidth, $maxHeight);

        $encoded  = $image->toWebp($quality);
        $filename = $basename . '.webp';
        $path     = $directory . '/' . $filename;

        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }
}
