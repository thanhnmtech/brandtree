<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model quản lý prompt tóm tắt tổng hợp
 * Bảng summary_agents lưu các prompt chạy ngầm khi hoàn thành điều kiện nhất định
 *
 * Các trigger_condition hỗ trợ:
 * - root_completed: khi tất cả root steps hoàn thành
 * - trunk_completed: khi tất cả trunk steps hoàn thành
 * - all_completed: khi tất cả root + trunk steps hoàn thành
 *
 * Các input_source hỗ trợ:
 * - root: chỉ dùng root_data
 * - trunk: chỉ dùng trunk_data
 * - root+trunk: dùng cả root_data + trunk_data
 */
class SummaryAgent extends Model
{
    protected $table = 'summary_agents';

    protected $fillable = [
        'name',
        'prompt',
        'trigger_condition',
        'input_source',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Trigger condition constants
     */
    const TRIGGER_ROOT_COMPLETED = 'root_completed';
    const TRIGGER_TRUNK_COMPLETED = 'trunk_completed';
    const TRIGGER_ALL_COMPLETED = 'all_completed';

    /**
     * Input source constants
     */
    const INPUT_ROOT = 'root';
    const INPUT_TRUNK = 'trunk';
    const INPUT_ROOT_TRUNK = 'root+trunk';

    /**
     * Lấy prompt theo name
     */
    public static function getByName(string $name): ?self
    {
        return static::where('name', $name)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Lấy tất cả prompt active theo trigger condition
     */
    public static function getByTrigger(string $triggerCondition): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('trigger_condition', $triggerCondition)
            ->where('is_active', true)
            ->get();
    }
}
