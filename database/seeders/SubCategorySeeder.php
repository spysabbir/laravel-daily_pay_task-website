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
                'name' => 'Youtube Sub Category 1',
                'slug' => 'youtube-sub-category-1',
            ],
            [
                'category_id' => 1,
                'name' => 'Youtube Sub Category 2',
                'slug' => 'youtube-sub-category-2',
            ],
            [
                'category_id' => 1,
                'name' => 'Youtube Sub Category 3',
                'slug' => 'youtube-sub-category-3',
            ],
            [
                'category_id' => 2,
                'name' => 'Facebook Sub Category 1',
                'slug' => 'facebook-sub-category-1',
            ],
            [
                'category_id' => 2,
                'name' => 'Facebook Sub Category 2',
                'slug' => 'facebook-sub-category-2',
            ],
            [
                'category_id' => 2,
                'name' => 'Facebook Sub Category 3',
                'slug' => 'facebook-sub-category-3',
            ],
            [
                'category_id' => 3,
                'name' => 'Instagram Sub Category 1',
                'slug' => 'instagram-sub-category-1',
            ],
            [
                'category_id' => 3,
                'name' => 'Instagram Sub Category 2',
                'slug' => 'instagram-sub-category-2',
            ],
            [
                'category_id' => 3,
                'name' => 'Instagram Sub Category 3',
                'slug' => 'instagram-sub-category-3',
            ]
        ];

        foreach ($sub_categories as $sub_category) {
            \App\Models\SubCategory::create($sub_category);
        }
    }
}
