<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model quản lý file đã upload cho RAG
 * Có thể attach vào Chat hoặc BrandAgent
 */
class UploadedFile extends Model
{
    protected $fillable = [
        'user_id',
        'brand_id',
        'attachable_type',
        'attachable_id',
        'filename',
        'file_path',
        'mime_type',
        'file_size',
        'original_content',
        'image_description',
        'status',
        'error_message',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // === Relationships ===

    /**
     * User đã upload file
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Brand liên quan (optional)
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Polymorphic: Chat hoặc BrandAgent
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Các chunks đã được tạo từ file này
     */
    public function chunks(): HasMany
    {
        return $this->hasMany(FileChunk::class);
    }

    // === Scopes ===

    /**
     * Chỉ lấy file đã hoàn tất xử lý
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Chỉ lấy file đang pending hoặc processing
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    /**
     * Lấy file của một Chat cụ thể
     */
    public function scopeForChat($query, int $chatId)
    {
        return $query->where('attachable_type', Chat::class)
            ->where('attachable_id', $chatId);
    }

    /**
     * Lấy file của một BrandAgent cụ thể
     */
    public function scopeForAgent($query, int $agentId)
    {
        return $query->where('attachable_type', BrandAgent::class)
            ->where('attachable_id', $agentId);
    }

    // === Helpers ===

    /**
     * Kiểm tra file đã xử lý xong chưa
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Kiểm tra file có phải hình ảnh không
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Lấy nội dung text đã extract (text hoặc image description)
     */
    public function getExtractedContent(): ?string
    {
        if ($this->isImage()) {
            return $this->image_description;
        }
        return $this->original_content;
    }
}
