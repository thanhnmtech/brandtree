<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Gói Khám phá',
                'slug' => 'kham-pha',
                'price' => 0,
                'credits' => 1000,
                'duration_days' => 14,
                'models_allowed' => json_encode(['GPT-4o mini']),
                'is_trial' => true,
                'is_popular' => false,
                'description' => 'Miễn phí trong 14 ngày. Trải nghiệm các tính năng cơ bản với 1.000 credit và mô hình GPT-4o mini.',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gói Nền tảng',
                'slug' => 'nen-tang',
                'price' => 990000,
                'credits' => 5000,
                'duration_days' => 30,
                'models_allowed' => json_encode(['GPT-4o mini', 'GPT-5 mini', 'GPT-5', 'GPT-4o']),
                'is_trial' => false,
                'is_popular' => false,
                'description' => '990.000đ/tháng. 5.000 credit với đầy đủ các mô hình AI tiên tiến.',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gói Chuyên nghiệp',
                'slug' => 'chuyen-nghiep',
                'price' => 1990000,
                'credits' => 12000,
                'duration_days' => 30,
                'models_allowed' => json_encode(['GPT-4o mini', 'GPT-5 mini', 'GPT-5', 'GPT-4o']),
                'is_trial' => false,
                'is_popular' => true,
                'description' => '1.990.000đ/tháng. 12.000 credit với đầy đủ các mô hình AI tiên tiến. Phổ biến nhất.',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('plans')->insert($plans);
    }
}
