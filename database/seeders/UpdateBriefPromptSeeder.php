<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgentSystem;

/**
 * Seeder an toàn cho production
 * Chỉ UPDATE cột brief_prompt cho các agent đã tồn tại
 * KHÔNG xóa, KHÔNG tạo mới, KHÔNG ảnh hưởng dữ liệu khác
 */
class UpdateBriefPromptSeeder extends Seeder
{
    public function run(): void
    {
        // Danh sách brief_prompt theo agent_type
        $briefPrompts = [
            'root1' => 'Đây là brief prompt gốc 1',
            'root2' => 'Đây là brief prompt gốc 2',
            'root3' => 'Đây là brief prompt gốc 3',
            'trunk1' => 'Đây là brief prompt thân 1',
            'trunk2' => 'Đây là brief prompt thân 2',
        ];

        foreach ($briefPrompts as $agentType => $briefPrompt) {
            $updated = AgentSystem::where('agent_type', $agentType)
                ->update(['brief_prompt' => $briefPrompt]);

            if ($updated) {
                $this->command->info("✅ Đã cập nhật brief_prompt cho agent: {$agentType}");
            } else {
                $this->command->warn("⚠️ Không tìm thấy agent: {$agentType}");
            }
        }
    }
}
