<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createCategories();  
    }

    private function createCategories()
    {
        foreach (Category::DEFAULT_CATEGORIES_WITH_TAGS as $category => $tags) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'tags' => $tags,
            ]);
        }
    }
}
