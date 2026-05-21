<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PointHistoryController extends Controller
{
    // 포인트 타입 → 한글 레이블
    private static array $typeLabels = [
        'create_post'          => '게시글 작성',
        'earn_comment_effort'  => '댓글 작성',
        'receive_comment'      => '내 글에 댓글 달림',
        'receive_like'         => '띱(좋아요) 받음',
        'receive_comment_like' => '댓글 좋아요 받음',
        'lost_like'            => '띱 취소됨',
        'lost_comment_like'    => '댓글 좋아요 취소됨',
        'revoke_post'          => '게시글 삭제로 회수',
        'revoke_comment'       => '댓글 삭제로 회수',
        'spend'                => '포인트 사용',
    ];

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user()->load('tier');

        $histories = PointHistory::where('user_id', $user->id)
            ->latest()
            ->paginate(20)
            ->through(function ($h) {
                return [
                    'id'         => $h->id,
                    'amount'     => $h->amount,
                    'type'       => $h->type,
                    'type_label' => self::$typeLabels[$h->type] ?? $h->type,
                    'created_at' => $h->created_at->format('Y.m.d H:i'),
                ];
            });

        // 다음 티어까지 남은 포인트 계산
        $nextTier = \App\Models\Tier::where('min_points', '>', $user->total_points)
            ->orderBy('min_points')
            ->first();

        $progress = null;
        if ($nextTier && $user->tier) {
            $currentMin = $user->tier->min_points;
            $range      = $nextTier->min_points - $currentMin;
            $earned     = $user->total_points - $currentMin;
            $progress   = [
                'next_tier_name'    => $nextTier->name,
                'next_tier_icon'    => $nextTier->icon_url,
                'next_tier_points'  => $nextTier->min_points,
                'remaining'         => $nextTier->min_points - $user->total_points,
                'percent'           => $range > 0 ? min(100, round($earned / $range * 100)) : 100,
            ];
        }

        return Inertia::render('Profile/Points', [
            'user'      => [
                'name'           => $user->name,
                'current_points' => $user->current_points,
                'total_points'   => $user->total_points,
                'tier'           => $user->tier ? [
                    'name'     => $user->tier->name,
                    'icon_url' => $user->tier->icon_url,
                    'min_points' => $user->tier->min_points,
                ] : null,
            ],
            'histories' => $histories,
            'progress'  => $progress,
        ]);
    }
}
