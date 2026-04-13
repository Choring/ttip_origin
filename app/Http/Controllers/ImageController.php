<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Handle image upload from Tiptap editor.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            try {
                $disk = config('filesystems.default');
                $path = $request->file('image')->store('content', $disk);
                
                if (!$path) {
                    return response()->json(['error' => '파일을 저장소에 저장하지 못했습니다.'], 500);
                }
                
                return response()->json([
                    'url' => Storage::disk($disk)->url($path)
                ]);
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                if ($e->getPrevious()) {
                    $errorMessage .= ' (운영체제/드라이버 에러: ' . $e->getPrevious()->getMessage() . ')';
                }
                \Log::error('이미지 업로드 중 오류 발생: ' . $errorMessage);
                return response()->json(['error' => '업로드 중 서버 오류가 발생했습니다: ' . $errorMessage], 500);
            }
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
