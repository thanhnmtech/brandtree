<?php
require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Brand;
use App\Jobs\SummarizeBrandDataJob;

$brand = Brand::first();
if ($brand) {
    echo "Dispatching job for brand: {$brand->name} (ID: {$brand->id})\n";
    SummarizeBrandDataJob::dispatch($brand->id, 'root1', 'Đây là nội dung phân tích chi tiết về văn hóa doanh nghiệp, bao gồm mục đích, giá trị cốt lõi, hành vi mong đợi, nghi lễ truyền thống và các biểu tượng. Là một cty tech startup 20 người, định hướng tạo sản phẩm công nghệ giúp đơn giản hóa quy trình kinh doanh cho SME. Giá trị cốt lõi: Sáng tạo, Chính trực, Lấy khách hàng làm trung tâm. Hành vi mong đợi: Làm việc chủ động, chia sẻ kiến thức, tôn trọng sự đa dạng. Nghi lễ: Daily standup, Quarterly review, Friday team building. Biểu tượng: Logo xanh lá với chiếc chìa khóa, Khẩu hiệu "Unlock Growth for SMEs".');
    echo "✓ Job dispatched\n";
} else {
    echo "✗ No brands found\n";
}
