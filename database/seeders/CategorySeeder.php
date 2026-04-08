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
            ['name' => 'IT/개발', 'slug' => 'tech', 'sort_order' => 1],
            ['name' => '생활/꿀팁', 'slug' => 'life', 'sort_order' => 2],
            ['name' => '디자인/기획', 'slug' => 'design', 'sort_order' => 3],
            ['name' => '취미/문화', 'slug' => 'culture', 'sort_order' => 4],
            ['name' => '여행', 'slug' => 'travel', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
            ['slug' => $category['slug']],
            ['name' => $category['name'], 'sort_order' => $category['sort_order']]
            );
        }
    }
}
