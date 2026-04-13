<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => '맛집', 'slug' => 'restaurant', 'sort_order' => 1],
            ['name' => '카페', 'slug' => 'cafe', 'sort_order' => 2],
            ['name' => '혼밥 맛집', 'slug' => 'solo-dining', 'sort_order' => 3],
            ['name' => '헬스장', 'slug' => 'gym', 'sort_order' => 4],
            ['name' => '알바', 'slug' => 'part-time', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
            ['slug' => $category['slug']],
            ['name' => $category['name'], 'sort_order' => $category['sort_order']]
            );
        }
    }
}
