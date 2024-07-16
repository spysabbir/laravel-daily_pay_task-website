<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Youtube',
                'slug' => 'youtube',
            ],
            [
                'name' => 'Facebook',
                'slug' => 'facebook',
            ],
            [
                'name' => 'Instagram',
                'slug' => 'instagram',
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
