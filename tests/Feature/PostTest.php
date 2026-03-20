<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Tier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tier::create(['name' => '씨앗', 'min_points' => 0]);
        Tier::create(['name' => '새싹', 'min_points' => 100]);
    }

    public function test_user_can_create_a_post(): void
    {
        $user = User::factory()->create(['current_points' => 0, 'tier_id' => 1]);

        $response = $this->actingAs($user)->post('/posts', [
            'title' => 'Test Title',
            'content' => 'Test Content',
        ]);

        $response->assertRedirect('/')->assertSessionHas('success');
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Title',
            'user_id' => $user->id,
        ]);
        
        // 10 points awarded
        $this->assertEquals(10, $user->fresh()->current_points);
    }

    public function test_user_cannot_update_others_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $post = Post::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->put("/posts/{$post->id}", [
            'title' => 'Hacked Title',
            'content' => 'Hacked Content',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('posts', ['title' => 'Hacked Title']);
    }
}
