<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAiLog extends Model
{
    protected $fillable = [
        'user_id',
        'chat_id',
        'brand_id',
        'brand_name',
        'agent_id',
        'agent_name',
        'agent_type',
        'model',
        'final_user_content',
        'final_system_instruction',
        'ai_response',
        'input_tokens',
        'output_tokens',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Chat
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
