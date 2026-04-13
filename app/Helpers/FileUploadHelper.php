<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadHelper
{
    /**
     * 지정된 폴더(local -> public disk)에 업로드된 파일을 고유한 이름으로 저장합니다.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string 저장된 파일의 DB 경로 (예: posts/abcdef1234_1700000000.jpg)
     */
    public static function upload(UploadedFile $file, string $directory = 'uploads'): string
    {
        $filename = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // .env의 FILESYSTEM_DISK 설정에 따라 저장 (local, public, s3 등)
        $disk = config('filesystems.default');
        return $file->storeAs($directory, $filename, $disk);
    }
}
