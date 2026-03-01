<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AgentSystem extends Model
{
    use HasFactory;

    // Khai báo tên bảng vì không theo chuẩn (agent_systems)
    protected $table = 'system_agent';

    protected $fillable = [
        'is_template',
        'agent_type',
        'name',
        'target',
        'output_description',
        'assistant_id',
        'assistant_key',
        'vector_id', // <-- MỚI: ID của Vector Store/Knowledge Base
        'model',     // <-- MỚI: Tên model AI (gpt-4o, gpt-3.5...)
        'prompt',
        'brief_prompt', // Prompt dùng để gọi OpenAI tóm tắt nội dung phân tích
        'ui_display',
        'status',
        'step_order',
    ];

    protected $casts = [
        'is_template' => 'boolean',
        'step_order' => 'integer',
        'ui_display' => 'array', // Tự động convert JSON <-> Array
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'assistant_key' => 'encrypted', // Bỏ comment dòng này nếu bạn muốn mã hóa Key trong DB
    ];

    /**
     * Agent Type Constants
     */
    const TYPE_ROOT_1 = 'root1';
    const TYPE_ROOT_2 = 'root2';
    const TYPE_ROOT_3 = 'root3';
    const TYPE_TRUNK_1 = 'trunk1';
    const TYPE_TRUNK_2 = 'trunk2';

    /**
     * Status Constants
     */
    const STATUS_LOCKED = 'locked';
    const STATUS_ACTIVE = 'active';
    const STATUS_DONE = 'done';

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Only templates
     */
    public function scopeTemplates(Builder $query): Builder
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope: Only active agents
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('agent_type', $type);
    }
    
    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Check if agent is locked
     */
    public function isLocked(): bool
    {
        return $this->status === self::STATUS_LOCKED;
    }

    /**
     * Check if agent is a template
     */
    public function isTemplate(): bool
    {
        return $this->is_template === true;
    }

    /**
     * Get a setting from ui_display safely
     */
    public function getUiSetting(string $key, $default = null)
    {
        return data_get($this->ui_display, $key, $default);
    }

    /**
     * Helper: Lấy Model AI sử dụng (nếu null trả về mặc định)
     */
    public function getAiModel(): string
    {
        return $this->model ?? 'gpt-4o';
    }
}