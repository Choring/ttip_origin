<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TierController extends Controller
{
    public function index()
    {
        $tiers = Tier::withCount('users')
            ->orderBy('min_points')
            ->get();

        return Inertia::render('Admin/Tiers/Index', [
            'tiers' => $tiers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:20|unique:tiers,name',
            'min_points' => 'required|integer|min:0|unique:tiers,min_points',
            'icon_url'   => 'nullable|string|max:10',
        ]);

        Tier::create($validated);

        // 새 티어 추가 후 전체 유저 티어 재계산
        $this->recalculateAllUserTiers();

        return back()->with('success', '새 티어가 추가되었습니다.');
    }

    public function update(Request $request, Tier $tier)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:20|unique:tiers,name,' . $tier->id,
            'min_points' => 'required|integer|min:0|unique:tiers,min_points,' . $tier->id,
            'icon_url'   => 'nullable|string|max:10',
        ]);

        $tier->update($validated);

        // 포인트 기준 변경 시 전체 유저 티어 재계산
        $this->recalculateAllUserTiers();

        return back()->with('success', "'{$tier->name}' 티어가 수정되었습니다.");
    }

    public function destroy(Tier $tier)
    {
        // 해당 티어를 사용 중인 유저가 있으면 삭제 불가
        if ($tier->users()->exists()) {
            return back()->with('error', "현재 {$tier->users_count}명이 이 티어를 사용 중입니다. 삭제할 수 없습니다.");
        }

        $tier->delete();

        return back()->with('success', "'{$tier->name}' 티어가 삭제되었습니다.");
    }

    /**
     * 전체 유저의 티어를 total_points 기준으로 재계산합니다.
     * 티어 기준이 변경됐을 때 호출됩니다.
     */
    private function recalculateAllUserTiers(): void
    {
        $tiers = Tier::orderBy('min_points', 'desc')->get();

        User::chunk(200, function ($users) use ($tiers) {
            foreach ($users as $user) {
                $newTier = $tiers->firstWhere(fn($t) => $t->min_points <= $user->total_points);
                if ($newTier && $user->tier_id !== $newTier->id) {
                    $user->tier_id = $newTier->id;
                    $user->save();
                }
            }
        });
    }
}
