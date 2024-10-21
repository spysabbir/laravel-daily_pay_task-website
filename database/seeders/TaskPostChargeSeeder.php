<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskPostChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $task_post_charges = [
            [
                'category_id' => 1,
                'sub_category_id' => 1,
                'min_charge' => 2,
                'max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 2,
                'min_charge' => 2.20,
                'max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 3,
                'min_charge' => 2.50,
                'max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 1,
                'min_charge' => 2.70,
                'max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 2,
                'min_charge' => 2.80,
                'max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 3,
                'min_charge' => 2.90,
                'max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 4,
                'min_charge' => 3,
                'max_charge' => 20,
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 5,
                'min_charge' => 6,
                'max_charge' => 9,
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 6,
                'min_charge' => 7,
                'max_charge' => 10,
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 7,
                'min_charge' => 8,
                'max_charge' => 11,
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 7,
                'min_charge' => 9,
                'max_charge' => 12,
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 9,
                'min_charge' => 10,
                'max_charge' => 13,
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 10,
                'min_charge' => 11,
                'max_charge' => 14,
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 11,
                'min_charge' => 12,
                'max_charge' => 15,
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 12,
                'min_charge' => 13,
                'max_charge' => 16,
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 13,
                'min_charge' => 0,
                'max_charge' => 0,
            ],
        ];

        foreach ($task_post_charges as $task_post_charge) {
            \App\Models\TaskPostCharge::create($task_post_charge);
        }
    }
}
