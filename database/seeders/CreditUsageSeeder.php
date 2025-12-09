<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CreditUsage;
use App\Models\User;
use Illuminate\Database\Seeder;

class CreditUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brand = Brand::find(1);
        if (!$brand) {
            $this->command->error('Brand với ID 1 không tồn tại!');
            return;
        }

        $users = User::whereHas('brandMemberships', function ($query) use ($brand) {
            $query->where('brand_id', $brand->id);
        })->get();

        if ($users->isEmpty()) {
            $this->command->error('Không tìm thấy user nào cho Brand ID 1!');
            return;
        }

        $subscription = $brand->activeSubscription;

        $models = ['GPT-4', 'GPT-4o', 'GPT-4o mini', 'Claude 3.5 Sonnet', 'Claude 3 Opus'];
        $actions = [
            'chat' => 'Tạo nội dung marketing',
            'brand_analysis' => 'Phân tích thương hiệu SWOT',
            'content_generation' => 'Tạo bài viết blog',
            'brand_strategy' => 'Lập chiến lược thương hiệu',
            'competitor_analysis' => 'Phân tích đối thủ',
            'market_research' => 'Nghiên cứu thị trường',
            'social_media_content' => 'Tạo nội dung social media',
            'email_campaign' => 'Tạo chiến dịch email',
        ];

        $this->command->info('Đang tạo credit usages cho Brand ID 1...');

        // Tạo 100 records trong 30 ngày qua
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $actionType = array_rand($actions);
            $description = $actions[$actionType];
            $model = $models[array_rand($models)];

            // Random credits: 5-50 credits
            $amount = rand(5, 50);

            // Random date trong 30 ngày qua
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            CreditUsage::create([
                'brand_id' => $brand->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription?->id,
                'amount' => $amount,
                'model_used' => $model,
                'action_type' => $actionType,
                'description' => $description,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // Thêm một số records credit addition (negative amount)
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));

            CreditUsage::create([
                'brand_id' => $brand->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription?->id,
                'amount' => -rand(100, 500), // Negative = thêm credits
                'model_used' => null,
                'action_type' => 'credit_purchase',
                'description' => 'Mua thêm credits',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('✅ Đã tạo 110 credit usage records cho Brand ID 1!');
    }
}
