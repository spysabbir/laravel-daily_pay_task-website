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
                'sub_category_id' => 1,
                'name' => 'Youtube Child Category 1',
                'slug' => 'youtube-child-category-1',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 1,
                'name' => 'Youtube Child Category 2',
                'slug' => 'youtube-child-category-2',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 1,
                'name' => 'Youtube Child Category 3',
                'slug' => 'youtube-child-category-3',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 2,
                'name' => 'Youtube Child Category 4',
                'slug' => 'youtube-child-category-4',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 2,
                'name' => 'Youtube Child Category 5',
                'slug' => 'youtube-child-category-5',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 2,
                'name' => 'Youtube Child Category 6',
                'slug' => 'youtube-child-category-6',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 3,
                'name' => 'Youtube Child Category 7',
                'slug' => 'youtube-child-category-7',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 3,
                'name' => 'Youtube Child Category 8',
                'slug' => 'youtube-child-category-8',
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 3,
                'name' => 'Youtube Child Category 9',
                'slug' => 'youtube-child-category-9',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 4,
                'name' => 'Facebook Child Category 1',
                'slug' => 'facebook-child-category-1',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 4,
                'name' => 'Facebook Child Category 2',
                'slug' => 'facebook-child-category-2',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 4,
                'name' => 'Facebook Child Category 3',
                'slug' => 'facebook-child-category-3',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 5,
                'name' => 'Facebook Child Category 4',
                'slug' => 'facebook-child-category-4',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 5,
                'name' => 'Facebook Child Category 5',
                'slug' => 'facebook-child-category-5',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 5,
                'name' => 'Facebook Child Category 6',
                'slug' => 'facebook-child-category-6',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 6,
                'name' => 'Facebook Child Category 7',
                'slug' => 'facebook-child-category-7',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 6,
                'name' => 'Facebook Child Category 8',
                'slug' => 'facebook-child-category-8',
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 6,
                'name' => 'Facebook Child Category 9',
                'slug' => 'facebook-child-category-9',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 7,
                'name' => 'Instagram Child Category 1',
                'slug' => 'instagram-child-category-1',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 7,
                'name' => 'Instagram Child Category 2',
                'slug' => 'instagram-child-category-2',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 7,
                'name' => 'Instagram Child Category 3',
                'slug' => 'instagram-child-category-3',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 8,
                'name' => 'Instagram Child Category 4',
                'slug' => 'instagram-child-category-4',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 8,
                'name' => 'Instagram Child Category 5',
                'slug' => 'instagram-child-category-5',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 8,
                'name' => 'Instagram Child Category 6',
                'slug' => 'instagram-child-category-6',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 9,
                'name' => 'Instagram Child Category 7',
                'slug' => 'instagram-child-category-7',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 9,
                'name' => 'Instagram Child Category 8',
                'slug' => 'instagram-child-category-8',
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 9,
                'name' => 'Instagram Child Category 9',
                'slug' => 'instagram-child-category-9',
            ],
        ];

        foreach ($child_categories as $child_category) {
            \App\Models\ChildCategory::create($child_category);
        }

    }
}
