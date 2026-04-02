<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. 초기 티어(Tier) 더미 데이터 생성
        $tiers = [
            ['name' => '씨앗', 'min_points' => 0],
            ['name' => '새싹', 'min_points' => 100],
            ['name' => '잎새', 'min_points' => 500],
            ['name' => '가지', 'min_points' => 1500],
            ['name' => '열매', 'min_points' => 3000],
            ['name' => '거목', 'min_points' => 10000],
        ];
        
        foreach ($tiers as $tier) {
            \App\Models\Tier::firstOrCreate(['name' => $tier['name']], $tier);
        }

        // 2. 관리자 겸 테스트용 계정 생성
        $testUser = \App\Models\User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '테스트유저(관리자)',
                'password' => bcrypt('password'),
                'current_points' => 500,
                'tier_id' => \App\Models\Tier::where('name', '잎새')->first()->id ?? 1,
                'role' => 'master',
            ]
        );

        // 3. 더미 유저 기생성 방지용 체크 (새로 생성 환경을 위해 factory 호출)
        if (\App\Models\User::count() < 10) {
            $users = \App\Models\User::factory(10)->create();
        } else {
            $users = \App\Models\User::all();
        }

        // 4. 랜덤 게시글 생성
        foreach ($users as $user) {
            if ($user->posts()->count() === 0) { // 이미 있으면 패스
                \App\Models\Post::factory(rand(2, 5))->create([
                    'user_id' => $user->id
                ]);
            }
        }
    }
}
