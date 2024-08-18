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
                'slug' => 'subscribed',
            ],
            [
                'category_id' => 1,
                'name' => 'Liked',
                'slug' => 'liked',
            ],
            [
                'category_id' => 1,
                'name' => 'Commented',
                'slug' => 'commented',
            ],
            [
                'category_id' => 1,
                'name' => 'Watched',
                'slug' => 'watched',
            ],
            // [
            //     'category_id' => 2,
            //     'name' => 'Followed',
            //     'slug' => 'followed',
            // ],
            // [
            //     'category_id' => 2,
            //     'name' => 'Liked',
            //     'slug' => 'liked',
            // ],
            // [
            //     'category_id' => 2,
            //     'name' => 'Commented',
            //     'slug' => 'commented',
            // ],
            // [
            //     'category_id' => 2,
            //     'name' => 'Watched',
            //     'slug' => 'watched',
            // ],
            // [
            //     'category_id' => 3,
            //     'name' => 'Followed',
            //     'slug' => 'followed',
            // ],
            // [
            //     'category_id' => 3,
            //     'name' => 'Liked',
            //     'slug' => 'liked',
            // ],
            // [
            //     'category_id' => 3,
            //     'name' => 'Commented',
            //     'slug' => 'commented',
            // ],
            // [
            //     'category_id' => 3,
            //     'name' => 'Watched',
            //     'slug' => 'watched',
            // ],
            // [
            //     'category_id' => 4,
            //     'name' => 'Followed',
            //     'slug' => 'followed',
            // ],
            // [
            //     'category_id' => 4,
            //     'name' => 'Liked',
            //     'slug' => 'liked',
            // ],
            // [
            //     'category_id' => 4,
            //     'name' => 'Commented',
            //     'slug' => 'commented',
            // ],
            // [
            //     'category_id' => 4,
            //     'name' => 'Watched',
            //     'slug' => 'watched',
            // ],
            // [
            //     'category_id' => 5,
            //     'name' => 'Followed',
            //     'slug' => 'followed',
            // ],
            // [
            //     'category_id' => 5,
            //     'name' => 'Liked',
            //     'slug' => 'liked',
            // ],
            // [
            //     'category_id' => 5,
            //     'name' => 'Commented',
            //     'slug' => 'commented',
            // ],
            // [
            //     'category_id' => 5,
            //     'name' => 'Watched',
            //     'slug' => 'watched',
            // ],
            [
                'category_id' => 2,
                'name' => 'N/A',
                'slug' => 'na',
            ]
        ];

        foreach ($sub_categories as $sub_category) {
            \App\Models\SubCategory::create($sub_category);
        }
    }
}
