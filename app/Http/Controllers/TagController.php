<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * 태그 자동완성 추천
     * GET /api/tags/suggest?q=검색어
     */
    public function suggest(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $tags = DB::table('post_tags')
            ->select('tag', DB::raw('COUNT(*) as count'))
            ->where('tag', 'like', $q . '%')
            ->groupBy('tag')
            ->orderByDesc('count')
            ->limit(8)
            ->pluck('count', 'tag');

        $result = $tags->map(fn($count, $tag) => [
            'tag'   => $tag,
            'count' => $count,
        ])->values();

        return response()->json($result);
    }
}
