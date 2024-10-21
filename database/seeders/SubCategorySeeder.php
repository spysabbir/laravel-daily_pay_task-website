<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sub_categories = [
            [
                'category_id' => 1,
                'name' => 'Subscribed',
            ],
            [
                'category_id' => 1,
                'name' => 'Liked',
            ],
            [
                'category_id' => 1,
                'name' => 'Commented',
            ],
            [
                'category_id' => 1,
                'name' => 'Watched',
            ],
            [
                'category_id' => 2,
                'name' => 'Followed',
            ],
            [
                'category_id' => 2,
                'name' => 'Liked',
            ],
            [
                'category_id' => 2,
                'name' => 'Commented',
            ],
            [
                'category_id' => 2,
                'name' => 'Watched',
            ],
            [
                'category_id' => 3,
                'name' => 'Joined',
            ],
            [
                'category_id' => 3,
                'name' => 'Liked',
            ],
            [
                'category_id' => 3,
                'name' => 'Commented',
            ],
            [
                'category_id' => 3,
                'name' => 'Watched',
            ],
            [
                'category_id' => 4,
                'name' => 'N/A',
            ]
        ];

        foreach ($sub_categories as $sub_category) {
            \App\Models\SubCategory::create($sub_category);
        }
    }
}
