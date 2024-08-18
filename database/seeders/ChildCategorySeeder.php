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
                'slug' => 'under-1-minute',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'name' => '1-5 Minutes',
                'slug' => '1-5-minutes',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'name' => '5-10 Minutes',
                'slug' => '5-10-minutes',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'name' => '10-30 Minutes',
                'slug' => '10-30-minutes',
            ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'name' => 'Under 1 Minute',
            //     'slug' => 'under-1-minute',
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'name' => '1-5 Minutes',
            //     'slug' => '1-5-minutes',
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'name' => '5-10 Minutes',
            //     'slug' => '5-10-minutes',
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'name' => '10-30 Minutes',
            //     'slug' => '10-30-minutes',
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'name' => 'Under 1 Minute',
            //     'slug' => 'under-1-minute',
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'name' => '1-5 Minutes',
            //     'slug' => '1-5-minutes',
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'name' => '5-10 Minutes',
            //     'slug' => '5-10-minutes',
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'name' => '10-30 Minutes',
            //     'slug' => '10-30-minutes',
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'name' => 'Under 1 Minute',
            //     'slug' => 'under-1-minute',
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'name' => '1-5 Minutes',
            //     'slug' => '1-5-minutes',
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'name' => '5-10 Minutes',
            //     'slug' => '5-10-minutes',
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'name' => '10-30 Minutes',
            //     'slug' => '10-30-minutes',
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'name' => 'Under 1 Minute',
            //     'slug' => 'under-1-minute',
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'name' => '1-5 Minutes',
            //     'slug' => '1-5-minutes',
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'name' => '5-10 Minutes',
            //     'slug' => '5-10-minutes',
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'name' => '10-30 Minutes',
            //     'slug' => '10-30-minutes',
            // ],
        ];

        foreach ($child_categories as $child_category) {
            \App\Models\ChildCategory::create($child_category);
        }

    }
}
