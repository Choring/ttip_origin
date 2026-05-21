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
     * 포인트를 지급합니다.
     * - current_points (잔액): 증가
     * - total_points  (누적 획득량): 증가 → 티어 기준으로 사용
     *
     * @return Tier|null 티어가 승급된 경우 새 Tier 반환, 아니면 null
     */
    public function addPoints(User $user, int $amount, string $type, ?string $referenceTable = null, ?int $referenceId = null): ?Tier
    {
        $upgradedTier = null;

        DB::transaction(function () use ($user, $amount, $type, $referenceTable, $referenceId, &$upgradedTier) {
            PointHistory::create([
                'user_id'         => $user->id,
                'amount'          => $amount,
                'type'            => $type,
                'reference_table' => $referenceTable,
                'reference_id'    => $referenceId,
            ]);

            $user->increment('current_points', $amount);
            $user->increment('total_points', $amount);
            $user->refresh();

            $upgradedTier = $this->updateUserTier($user);
        });

        return $upgradedTier;
    }

    /**
     * 포인트를 차감합니다. (좋아요 취소 등 활동 반영)
     * - current_points: 감소
     * - total_points:   감소 (활동이 취소된 것이므로 누적량도 되돌림)
     *
     * ⚠️ 포인트 "소비/사용" 기능(미래 상점 등)은 별도 spendPoints() 사용
     */
    public function subtractPoints(User $user, int $amount, string $type, ?string $referenceTable = null, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($user, $amount, $type, $referenceTable, $referenceId) {
            PointHistory::create([
                'user_id'         => $user->id,
                'amount'          => -$amount,
                'type'            => $type,
                'reference_table' => $referenceTable,
                'reference_id'    => $referenceId,
            ]);

            $user->current_points = max(0, $user->current_points - $amount);
            $user->total_points   = max(0, $user->total_points   - $amount);
            $user->save();

            $this->updateUserTier($user);
        });
    }

    /**
     * 포인트를 소비합니다. (상점, 이벤트 등 미래 기능용)
     * - current_points: 감소
     * - total_points:   변동 없음 → 티어 유지
     */
    public function spendPoints(User $user, int $amount, string $type, ?string $referenceTable = null, ?int $referenceId = null): bool
    {
        if ($user->current_points < $amount) {
            return false; // 잔액 부족
        }

        DB::transaction(function () use ($user, $amount, $type, $referenceTable, $referenceId) {
            PointHistory::create([
                'user_id'         => $user->id,
                'amount'          => -$amount,
                'type'            => $type,
                'reference_table' => $referenceTable,
                'reference_id'    => $referenceId,
            ]);

            $user->current_points = max(0, $user->current_points - $amount);
            $user->save();
            // total_points 변경 없음 → 티어 유지
        });

        return true;
    }

    /**
     * 게시글 삭제 시 관련 포인트 전체 회수
     * current_points + total_points 모두 감소 (활동 자체가 사라지므로)
     */
    public function revokePostPoints(Post $post): void
    {
        $commentIds = Comment::where('post_id', $post->id)->pluck('id')->toArray();

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
                $user->total_points   = max(0, $user->total_points   - $totalRevoke);
                $user->save();
                $this->updateUserTier($user);
            }
        });
    }

    /**
     * 댓글 삭제 시 관련 포인트 회수
     * current_points + total_points 모두 감소
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
                $user->total_points   = max(0, $user->total_points   - $totalRevoke);
                $user->save();
                $this->updateUserTier($user);
            }
        });
    }

    /**
     * total_points 기준으로 티어를 갱신합니다.
     *
     * @return Tier|null 승급된 경우 새 Tier 반환, 강등/유지 시 null
     */
    public function updateUserTier(User $user): ?Tier
    {
        $oldTierId = $user->tier_id;

        $newTier = Tier::where('min_points', '<=', $user->total_points)
            ->orderBy('min_points', 'desc')
            ->first();

        if ($newTier && $user->tier_id !== $newTier->id) {
            $isUpgrade = $newTier->min_points > ($user->tier?->min_points ?? 0);
            $user->tier_id = $newTier->id;
            $user->save();

            // 승급한 경우에만 Tier 반환 (강등은 null)
            return $isUpgrade ? $newTier : null;
        }

        return null;
    }
}
