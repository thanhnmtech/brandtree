<?php

namespace App\Console\Commands;

use App\Jobs\SummarizeBrandDataJob;
use App\Models\Brand;
use Illuminate\Console\Command;

class TestSummarizeJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-summarize-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch a test SummarizeBrandDataJob to verify brief_prompt functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $brand = Brand::first();
        if (!$brand) {
            $this->error('No brands found in database');
            return 1;
        }

        $testContent = 'Đây là nội dung phân tích chi tiết về văn hóa doanh nghiệp, bao gồm mục đích, giá trị cốt lõi, hành vi mong đợi, nghi lễ truyền thống và các biểu tượng. Là một công ty công nghệ startup 20 người, định hướng tạo sản phẩm công nghệ giúp đơn giản hóa quy trình kinh doanh cho SME. Giá trị cốt lõi: Sáng tạo, Chính trực, Lấy khách hàng làm trung tâm. Hành vi mong đợi: Làm việc chủ động, chia sẻ kiến thức, tôn trọng sự đa dạng. Nghi lễ: Daily standup, Quarterly review, Friday team building. Biểu tượng: Logo xanh lá với chiếc chìa khóa, Khẩu hiệu "Unlock Growth for SMEs".';

        $this->info("Dispatching test SummarizeBrandDataJob...");
        $this->info("Brand: {$brand->name} (ID: {$brand->id})");
        $this->info("Agent Type: root1");
        $this->info("Content length: " . strlen($testContent));

        SummarizeBrandDataJob::dispatch($brand->id, 'root1', $testContent);

        $this->info("✓ Job dispatched successfully!");
        $this->info("Run: php artisan queue:work --max-jobs=1 --timeout=120");
        
        return 0;
    }
}

