<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->realText(30),
            'content' => fake()->realText(300),
            'summary' => [
                fake()->realText(20),
                fake()->realText(20),
                fake()->realText(20),
            ],
            'view_count' => fake()->numberBetween(0, 5000),
            'type' => 'general',
        ];
    }
}
