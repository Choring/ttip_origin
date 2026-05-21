<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use App\Models\PointHistory;
use App\Models\Tier;
use Illuminate\Support\Facades\DB;

class PointService
{
    /**
     * 사용자에게 포인트를 지급/차감하고 내역을 남긴 뒤 티어를 갱신합니다.
     */
    public function addPoints(User $user, int $amount, string $type, ?string $referenceTable = null, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($user, $amount, $type, $referenceTable, $referenceId) {
            // 1. 포인트 내역 생성
            PointHistory::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'reference_table' => $referenceTable,
                'reference_id' => $referenceId,
            ]);

            // 2. 유저 포인트 업데이트
            $user->increment('current_points', $amount);

            // 3. 티어 변동 체크
            $this->updateUserTier($user);
        });
    }

    /**
     * 사용자로부터 포인트를 회수하고 내역을 남긴 뒤 티어를 갱신합니다.
     */
    public function subtractPoints(User $user, int $amount, string $type, ?string $referenceTable = null, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($user, $amount, $type, $referenceTable, $referenceId) {
            // 1. 포인트 내역 생성 (음수로 기록)
            PointHistory::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => $type,
                'reference_table' => $referenceTable,
                'reference_id' => $referenceId,
            ]);

            // 2. 유저 포인트 업데이트 (0 이하로 떨어지지 않게 처리)
            $user->current_points = max(0, $user->current_points - $amount);
            $user->save();

            // 3. 티어 변동 체크
            $this->updateUserTier($user);
        });
    }

    /**
     * 게시글 삭제 시 해당 글과 관련된 모든 포인트를 회수합니다.
     * (글 작성 포인트 + 좋아요 수신 포인트 + 댓글 관련 포인트 전부 포함)
     */
    public function revokePostPoints(Post $post): void
    {
        // 이 게시글의 댓글 ID 목록
        $commentIds = Comment::where('post_id', $post->id)->pluck('id')->toArray();

        // 게시글/댓글에서 실제로 적립된 포인트 내역(양수만) 조회
        $query = PointHistory::where('amount', '>', 0)
            ->where(function ($q) use ($post, $commentIds) {
                $q->where(function ($q2) use ($post) {
                    $q2->where('reference_table', 'posts')
                       ->where('reference_id', $post->id);
                });
                if (!empty($commentIds)) {
                    $q->orWhere(function ($q2) use ($commentIds) {
                        $q2->where('reference_table', 'comments')
                           ->whereIn('reference_id', $commentIds);
                    });
                }
            });

        $histories = $query->get();
        if ($histories->isEmpty()) return;

        // 유저별로 회수할 포인트 합산 후 일괄 처리
        DB::transaction(function () use ($histories, $post) {
            foreach ($histories->groupBy('user_id') as $userId => $userHistories) {
                $totalRevoke = $userHistories->sum('amount');
                if ($totalRevoke <= 0) continue;

                $user = User::find($userId);
                if (!$user) continue;

                PointHistory::create([
                    'user_id'         => $userId,
                    'amount'          => -$totalRevoke,
                    'type'            => 'revoke_post',
                    'reference_table' => 'posts',
                    'reference_id'    => $post->id,
                ]);

                $user->current_points = max(0, $user->current_points - $totalRevoke);
                $user->save();
                $this->updateUserTier($user);
            }
        });
    }

    /**
     * 댓글 삭제 시 해당 댓글과 관련된 포인트를 회수합니다.
     * (댓글 작성 노력 포인트 + 게시글 작성자 댓글 수신 포인트 + 댓글 좋아요 포인트)
     */
    public function revokeCommentPoints(Comment $comment): void
    {
        $histories = PointHistory::where('reference_table', 'comments')
            ->where('reference_id', $comment->id)
            ->where('amount', '>', 0)
            ->get();

        if ($histories->isEmpty()) return;

        DB::transaction(function () use ($histories, $comment) {
            foreach ($histories->groupBy('user_id') as $userId => $userHistories) {
                $totalRevoke = $userHistories->sum('amount');
                if ($totalRevoke <= 0) continue;

                $user = User::find($userId);
                if (!$user) continue;

                PointHistory::create([
                    'user_id'         => $userId,
                    'amount'          => -$totalRevoke,
                    'type'            => 'revoke_comment',
                    'reference_table' => 'comments',
                    'reference_id'    => $comment->id,
                ]);

                $user->current_points = max(0, $user->current_points - $totalRevoke);
                $user->save();
                $this->updateUserTier($user);
            }
        });
    }

    /**
     * 사용자의 현재 포인트에 맞춰 티어를 갱신합니다.
     */
    public function updateUserTier(User $user): void
    {
        // 최신 포인트를 기준으로 달성 가능한 가장 높은 티어 찾기
        $newTier = Tier::where('min_points', '<=', $user->current_points)
                    ->orderBy('min_points', 'desc')
                    ->first();

        // 현재 티어와 다르다면 승급/강등 처리
        if ($newTier && $user->tier_id !== $newTier->id) {
            $user->tier_id = $newTier->id;
            $user->save();
        }
    }
}
