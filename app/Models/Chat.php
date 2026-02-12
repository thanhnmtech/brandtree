<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'brand_id',
        'agent_id',
        'agent_type',
        'model',
        'title',
        'conversation_id',
        'total_tokens',
        'total_messages',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * File đã upload cho chat này (RAG)
     */
    public function uploadedFiles()
    {
        return $this->morphMany(UploadedFile::class, 'attachable');
    }
}
