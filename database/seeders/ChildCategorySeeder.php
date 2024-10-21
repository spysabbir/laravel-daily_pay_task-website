<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChildCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $child_categories = [
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'name' => 'Under 1 Minute',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'name' => '1-5 Minutes',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'name' => '5-10 Minutes',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'name' => '10-30 Minutes',
            ],
        ];

        foreach ($child_categories as $child_category) {
            \App\Models\ChildCategory::create($child_category);
        }

    }
}
