<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tier;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tier::create(['name' => '씨앗', 'min_points' => 0]);
        Tier::create(['name' => '새싹', 'min_points' => 100]);
        Tier::create(['name' => '잎새', 'min_points' => 500]);
    }

    public function test_add_points_increments_user_points(): void
    {
        $user = User::factory()->create(['current_points' => 0, 'tier_id' => 1]);
        $service = new PointService();

        $service->addPoints($user, 50, 'test_earn');

        $this->assertEquals(50, $user->fresh()->current_points);
        $this->assertDatabaseHas('point_histories', [
            'user_id' => $user->id,
            'amount' => 50,
            'type' => 'test_earn',
        ]);
    }

    public function test_user_is_promoted_when_reaching_min_points(): void
    {
        $user = User::factory()->create(['current_points' => 50, 'tier_id' => 1]); // 씨앗
        $service = new PointService();

        $service->addPoints($user, 60, 'test_earn');

        $this->assertEquals(110, $user->fresh()->current_points);
        $this->assertEquals(2, $user->fresh()->tier_id); // 새싹 ID
    }
}
