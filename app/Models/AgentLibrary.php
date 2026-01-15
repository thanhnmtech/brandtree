<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AgentLibrary extends Model
{
    use HasFactory;

    protected $table = 'agent_library';

    protected $fillable = [
        'code',
        'name',
        'description',
        'instruction', // Hướng dẫn sử dụng cho User
        'prompt',      // <--- MỚI: System Prompt cho AI
        'agent_key',   // <--- MỚI
        'vector_id',   // <--- MỚI
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'agent_key' => 'encrypted', // <--- MỚI: Mã hoá key khi lưu vào DB để bảo mật
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ... (Giữ nguyên các scope cũ)
}