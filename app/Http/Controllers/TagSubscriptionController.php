<?php

namespace App\Http\Controllers;

use App\Models\TagSubscription;
use Illuminate\Http\Request;

class TagSubscriptionController extends Controller
{
    /**
     * 태그 구독 토글 (구독 ↔ 구독 취소)
     * POST /tags/{tag}/subscribe
     */
    public function toggle(string $tag)
    {
        $user = auth()->user();

        $existing = TagSubscription::where('user_id', $user->id)
            ->where('tag', $tag)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            TagSubscription::create([
                'user_id' => $user->id,
                'tag'     => $tag,
            ]);
        }

        return redirect()->back();
    }

    /**
     * 현재 유저의 구독 태그 목록
     * GET /api/tags/subscribed
     */
    public function index(): JsonResponse
    {
        $tags = TagSubscription::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->pluck('tag');

        return response()->json($tags);
    }
}
