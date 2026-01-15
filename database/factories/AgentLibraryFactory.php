<?php

namespace Database\Factories;

use App\Models\AgentLibrary;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentLibraryFactory extends Factory
{
    protected $model = AgentLibrary::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->slug(2)),
            'name' => fake()->jobTitle(),
            'description' => fake()->sentence(),
            
            // Instruction: Hướng dẫn user cách dùng
            'instruction' => "Bước 1: Nhập chủ đề.\nBước 2: Bấm nút tạo.",
            
            // Prompt: Lệnh gửi cho AI
            'prompt' => fake()->paragraphs(2, true),
            
            'agent_key' => 'sk-' . fake()->uuid(),
            'vector_id' => fake()->optional(0.5)->bothify('vs_????????????'), // 50% có vector
            
            'is_active' => fake()->boolean(90),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}