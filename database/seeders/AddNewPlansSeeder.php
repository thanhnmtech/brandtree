<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class AddNewPlansSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing plan IDs
        $basicPlan = Plan::where('slug', 'nen-tang')->first();
        $proPlan = Plan::where('slug', 'chuyen-nghiep')->first();

        // Create yearly plans
        if ($basicPlan && !Plan::where('slug', 'nen-tang-nam')->exists()) {
            Plan::create([
                'name' => 'Gói Nền tảng (Năm)',
                'slug' => 'nen-tang-nam',
                'type' => Plan::TYPE_SUBSCRIPTION,
                'price' => 9900000,
                'original_price' => 11880000,
                'credits' => 60000,
                'duration_days' => 365,
                'billing_cycle' => Plan::BILLING_YEARLY,
                'parent_plan_id' => $basicPlan->id,
                'models_allowed' => ['GPT-4o mini', 'GPT-5 mini', 'GPT-5', 'GPT-4o'],
                'is_trial' => false,
                'is_popular' => false,
                'description' => 'Tiết kiệm 2 tháng khi thanh toán năm. 60.000 credit/năm.',
                'sort_order' => 4,
                'is_active' => true,
            ]);
        }

        if ($proPlan && !Plan::where('slug', 'chuyen-nghiep-nam')->exists()) {
            Plan::create([
                'name' => 'Gói Chuyên nghiệp (Năm)',
                'slug' => 'chuyen-nghiep-nam',
                'type' => Plan::TYPE_SUBSCRIPTION,
                'price' => 19900000,
                'original_price' => 23880000,
                'credits' => 144000,
                'duration_days' => 365,
                'billing_cycle' => Plan::BILLING_YEARLY,
                'parent_plan_id' => $proPlan->id,
                'models_allowed' => ['GPT-4o mini', 'GPT-5 mini', 'GPT-5', 'GPT-4o'],
                'is_trial' => false,
                'is_popular' => false,
                'description' => 'Tiết kiệm 2 tháng khi thanh toán năm. 144.000 credit/năm.',
                'sort_order' => 5,
                'is_active' => true,
            ]);
        }

        // Create credit packages
        $creditPackages = [
            [
                'name' => '1.000 Credits',
                'slug' => 'credit-1000',
                'price' => 99000,
                'original_price' => null,
                'credits' => 1000,
                'is_popular' => false,
                'description' => 'Nạp thêm 1.000 credits vào tài khoản.',
                'sort_order' => 10,
            ],
            [
                'name' => '5.000 Credits',
                'slug' => 'credit-5000',
                'price' => 450000,
                'original_price' => 495000,
                'credits' => 5000,
                'is_popular' => true,
                'description' => 'Tiết kiệm 10%. Nạp thêm 5.000 credits.',
                'sort_order' => 11,
            ],
            [
                'name' => '10.000 Credits',
                'slug' => 'credit-10000',
                'price' => 800000,
                'original_price' => 990000,
                'credits' => 10000,
                'is_popular' => false,
                'description' => 'Tiết kiệm 20%. Nạp thêm 10.000 credits.',
                'sort_order' => 12,
            ],
            [
                'name' => '50.000 Credits',
                'slug' => 'credit-50000',
                'price' => 3500000,
                'original_price' => 4950000,
                'credits' => 50000,
                'is_popular' => false,
                'description' => 'Tiết kiệm 30%. Nạp thêm 50.000 credits.',
                'sort_order' => 13,
            ],
        ];

        foreach ($creditPackages as $pkg) {
            if (!Plan::where('slug', $pkg['slug'])->exists()) {
                Plan::create(array_merge($pkg, [
                    'type' => Plan::TYPE_CREDIT_PACKAGE,
                    'billing_cycle' => null,
                    'duration_days' => 0, // Credit packages don't expire
                    'models_allowed' => null,
                    'is_trial' => false,
                    'is_active' => true,
                ]));
            }
        }
    }
}
