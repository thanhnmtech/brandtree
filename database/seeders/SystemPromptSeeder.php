<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemPrompt;

class SystemPromptSeeder extends Seeder
{
    /**
     * Seed dữ liệu cho bảng system_prompts
     * Chứa các prompt hệ thống được sử dụng trong chat
     */
    public function run(): void
    {
        $prompts = [
            [
                'short_code' => 'default_assistant',
                'prompt' => 'Bạn là trợ lý ảo.',
                'description' => 'Prompt mặc định khi không tìm thấy agent hoặc instruction từ database. Được sử dụng làm fallback khi agent không có instruction riêng.',
            ],
            [
                'short_code' => 'brand_data_intro',
                'prompt' => "\n\nHãy nhớ toàn bộ thông tin về thương hiệu bên dưới để tạo câu trả lời phù hợp:\n",
                'description' => 'Giới thiệu phần data thương hiệu (root_data + trunk_data). Sử dụng khi agentType === "canopy", agent có is_include = true, và brand có root_data hoặc trunk_data.',
            ],
            [
                'short_code' => 'context_steps_intro',
                'prompt' => "\n\nHãy ghi nhớ các thông tin thương hiệu đã xác nhận bên dưới để tạo câu trả lời tiếp theo phù hợp:\n",
                'description' => 'Thêm context từ các bước trước đó trong sequence (root1 → root2 → root3 → trunk1 → trunk2). Sử dụng khi chat với agent trong sequence và có data từ các bước trước.',
            ],
            [
                'short_code' => 'rag_context_intro',
                'prompt' => "\n\nCó thể tham khảo các tài liệu mẫu bên dưới, cho phép tự quyết định có sử dụng thông tin bên dưới hoặc không sử dụng tùy vào sự phù hợp. Các thông tin liên quan là:\n",
                'description' => 'Thêm kết quả tìm kiếm từ Vector Store (RAG). Chỉ sử dụng với Gemini model khi agent có vector_id và vector store search trả về kết quả.',
            ],
        ];

        foreach ($prompts as $promptData) {
            SystemPrompt::updateOrCreate(
                ['short_code' => $promptData['short_code']],
                $promptData
            );
        }
    }
}
