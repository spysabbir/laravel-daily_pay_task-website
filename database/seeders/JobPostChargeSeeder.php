<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobPostChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $job_post_charges = [
            [
                'category_id' => 1,
                'sub_category_id' => 1,
                'working_min_charge' => 2,
                'working_max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 2,
                'working_min_charge' => 2.20,
                'working_max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 3,
                'working_min_charge' => 2.50,
                'working_max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 1,
                'working_min_charge' => 2.70,
                'working_max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 2,
                'working_min_charge' => 2.80,
                'working_max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 3,
                'working_min_charge' => 2.90,
                'working_max_charge' => 20,
            ],
            [
                'category_id' => 1,
                'sub_category_id' => 4,
                'child_category_id' => 4,
                'working_min_charge' => 3,
                'working_max_charge' => 20,
            ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 1,
            //     'working_min_charge' => 6,
            //     'working_max_charge' => 9,
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 2,
            //     'working_min_charge' => 7,
            //     'working_max_charge' => 10,
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 3,
            //     'working_min_charge' => 8,
            //     'working_max_charge' => 11,
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 1,
            //     'working_min_charge' => 9,
            //     'working_max_charge' => 12,
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 2,
            //     'working_min_charge' => 10,
            //     'working_max_charge' => 13,
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 3,
            //     'working_min_charge' => 11,
            //     'working_max_charge' => 14,
            // ],
            // [
            //     'category_id' => 2,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 4,
            //     'working_min_charge' => 12,
            //     'working_max_charge' => 15,
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 1,
            //     'working_min_charge' => 10,
            //     'working_max_charge' => 13,
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 2,
            //     'working_min_charge' => 11,
            //     'working_max_charge' => 14,
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 3,
            //     'working_min_charge' => 12,
            //     'working_max_charge' => 15,
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 1,
            //     'working_min_charge' => 13,
            //     'working_max_charge' => 16,
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 2,
            //     'working_min_charge' => 14,
            //     'working_max_charge' => 17,
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 3,
            //     'working_min_charge' => 15,
            //     'working_max_charge' => 18,
            // ],
            // [
            //     'category_id' => 3,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 4,
            //     'working_min_charge' => 16,
            //     'working_max_charge' => 19,
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 1,
            //     'working_min_charge' => 14,
            //     'working_max_charge' => 17,
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 2,
            //     'working_min_charge' => 15,
            //     'working_max_charge' => 18,
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 3,
            //     'working_min_charge' => 16,
            //     'working_max_charge' => 19,
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 1,
            //     'working_min_charge' => 17,
            //     'working_max_charge' => 20,
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 2,
            //     'working_min_charge' => 18,
            //     'working_max_charge' => 21,
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 3,
            //     'working_min_charge' => 19,
            //     'working_max_charge' => 22,
            // ],
            // [
            //     'category_id' => 4,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 4,
            //     'working_min_charge' => 20,
            //     'working_max_charge' => 23,
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 1,
            //     'working_min_charge' => 18,
            //     'working_max_charge' => 21,
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 2,
            //     'working_min_charge' => 19,
            //     'working_max_charge' => 22,
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 3,
            //     'working_min_charge' => 20,
            //     'working_max_charge' => 23,
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 1,
            //     'working_min_charge' => 21,
            //     'working_max_charge' => 24,
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 2,
            //     'working_min_charge' => 22,
            //     'working_max_charge' => 25,
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 3,
            //     'working_min_charge' => 23,
            //     'working_max_charge' => 26,
            // ],
            // [
            //     'category_id' => 5,
            //     'sub_category_id' => 4,
            //     'child_category_id' => 4,
            //     'working_min_charge' => 24,
            //     'working_max_charge' => 27,
            // ],
            // [
            //     'category_id' => 6,
            //     'sub_category_id' => 6,
            //     'working_min_charge' => 0,
            //     'working_max_charge' => 0,
            // ],

        ];

        foreach ($job_post_charges as $job_post_charge) {
            \App\Models\JobPostCharge::create($job_post_charge);
        }
    }
}
