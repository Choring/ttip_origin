<?php

namespace App\Services;

use App\Models\User;
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
