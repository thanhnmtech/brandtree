<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'chat_id',
        'role',
        'content',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
