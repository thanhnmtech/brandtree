<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================
        // SUBSCRIPTION PLANS - MONTHLY
        // ============================================

        Plan::create([
            'name' => 'Gói Khám phá',
            'slug' => 'kham-pha',
            'type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 0,
            'original_price' => null,
            'credits' => 1000,
            'duration_days' => 14,
            'billing_cycle' => Plan::BILLING_MONTHLY,
            'models_allowed' => ['GPT-4o mini'],
            'is_trial' => true,
            'is_popular' => false,
            'description' => 'Miễn phí 14 ngày. Trải nghiệm các tính năng cơ bản với 1.000 credit.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $basicPlan = Plan::create([
            'name' => 'Gói Nền tảng',
            'slug' => 'nen-tang',
            'type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 990000,
            'original_price' => null,
            'credits' => 5000,
            'duration_days' => 30,
            'billing_cycle' => Plan::BILLING_MONTHLY,
            'models_allowed' => ['GPT-4o mini', 'GPT-5 mini', 'GPT-5', 'GPT-4o'],
            'is_trial' => false,
            'is_popular' => false,
            'description' => '5.000 credit/tháng với đầy đủ các mô hình AI tiên tiến.',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $proPlan = Plan::create([
            'name' => 'Gói Chuyên nghiệp',
            'slug' => 'chuyen-nghiep',
            'type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 1990000,
            'original_price' => null,
            'credits' => 12000,
            'duration_days' => 30,
            'billing_cycle' => Plan::BILLING_MONTHLY,
            'models_allowed' => ['GPT-4o mini', 'GPT-5 mini', 'GPT-5', 'GPT-4o'],
            'is_trial' => false,
            'is_popular' => true,
            'description' => '12.000 credit/tháng. Phổ biến nhất cho doanh nghiệp.',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // ============================================
        // SUBSCRIPTION PLANS - YEARLY (Giảm 2 tháng)
        // ============================================

        Plan::create([
            'name' => 'Gói Nền tảng (Năm)',
            'slug' => 'nen-tang-nam',
            'type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 9900000, // 10 tháng thay vì 12
            'original_price' => 11880000, // 12 tháng giá gốc
            'credits' => 60000, // 5000 x 12
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

        Plan::create([
            'name' => 'Gói Chuyên nghiệp (Năm)',
            'slug' => 'chuyen-nghiep-nam',
            'type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 19900000, // 10 tháng thay vì 12
            'original_price' => 23880000, // 12 tháng giá gốc
            'credits' => 144000, // 12000 x 12
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

        // ============================================
        // CREDIT PACKAGES (Nạp thêm credit)
        // ============================================

        Plan::create([
            'name' => '1.000 Credits',
            'slug' => 'credit-1000',
            'type' => Plan::TYPE_CREDIT_PACKAGE,
            'price' => 99000,
            'original_price' => null,
            'credits' => 1000,
            'duration_days' => null,
            'billing_cycle' => null,
            'models_allowed' => null,
            'is_trial' => false,
            'is_popular' => false,
            'description' => 'Nạp thêm 1.000 credits vào tài khoản.',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        Plan::create([
            'name' => '5.000 Credits',
            'slug' => 'credit-5000',
            'type' => Plan::TYPE_CREDIT_PACKAGE,
            'price' => 450000,
            'original_price' => 495000, // Giảm ~10%
            'credits' => 5000,
            'duration_days' => null,
            'billing_cycle' => null,
            'models_allowed' => null,
            'is_trial' => false,
            'is_popular' => true,
            'description' => 'Tiết kiệm 10%. Nạp thêm 5.000 credits.',
            'sort_order' => 11,
            'is_active' => true,
        ]);

        Plan::create([
            'name' => '10.000 Credits',
            'slug' => 'credit-10000',
            'type' => Plan::TYPE_CREDIT_PACKAGE,
            'price' => 800000,
            'original_price' => 990000, // Giảm ~20%
            'credits' => 10000,
            'duration_days' => null,
            'billing_cycle' => null,
            'models_allowed' => null,
            'is_trial' => false,
            'is_popular' => false,
            'description' => 'Tiết kiệm 20%. Nạp thêm 10.000 credits.',
            'sort_order' => 12,
            'is_active' => true,
        ]);

        Plan::create([
            'name' => '50.000 Credits',
            'slug' => 'credit-50000',
            'type' => Plan::TYPE_CREDIT_PACKAGE,
            'price' => 3500000,
            'original_price' => 4950000, // Giảm ~30%
            'credits' => 50000,
            'duration_days' => null,
            'billing_cycle' => null,
            'models_allowed' => null,
            'is_trial' => false,
            'is_popular' => false,
            'description' => 'Tiết kiệm 30%. Nạp thêm 50.000 credits.',
            'sort_order' => 13,
            'is_active' => true,
        ]);
    }
}
