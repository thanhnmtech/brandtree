<?php

namespace Database\Factories;

use App\Models\AgentSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AgentSystem>
 */
class AgentSystemFactory extends Factory
{
    protected $model = AgentSystem::class;

    public function definition(): array
    {
        return [
            'is_template' => fake()->boolean(20), // 20% là template
            'agent_type' => fake()->randomElement([
                AgentSystem::TYPE_ROOT_1,
                AgentSystem::TYPE_ROOT_2,
                AgentSystem::TYPE_ROOT_3,
                AgentSystem::TYPE_TRUNK_1,
                AgentSystem::TYPE_TRUNK_2,
            ]),
            'name' => fake()->jobTitle() . ' Agent',
            'target' => fake()->sentence(6),
            'output_description' => fake()->paragraph(2),
            
            // --- CẬP NHẬT MỚI ---
            // 30% cơ hội có vector_id (knowledge base), 70% là null
            'vector_id' => fake()->optional(0.3)->bothify('vs_????????????######'), 
            
            // Random model AI
            'model' => fake()->randomElement(['gpt-4o', 'gpt-4o-mini', 'gpt-3.5-turbo']),
            // --------------------

            'assistant_id' => 'asst_' . fake()->lexify('????????????????????'),
            'assistant_key' => fake()->sha256(),
            
            'prompt' => fake()->text(400),
            
            'ui_display' => [
                'icon' => fake()->randomElement(['robot', 'brain', 'sparkles', 'tree']),
                'color' => fake()->hexColor(),
                'show_in_sidebar' => fake()->boolean(),
                'input_placeholder' => fake()->sentence(),
            ],
            
            'status' => fake()->randomElement([
                AgentSystem::STATUS_LOCKED, 
                AgentSystem::STATUS_ACTIVE, 
                AgentSystem::STATUS_DONE
            ]),
            
            'step_order' => fake()->numberBetween(1, 10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Các state giữ nguyên
    public function template(): static
    {
        return $this->state(fn (array $attributes) => ['is_template' => true]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AgentSystem::STATUS_ACTIVE]);
    }
}