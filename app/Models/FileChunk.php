<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model quản lý chunks text và embedding vectors cho RAG search
 */
class FileChunk extends Model
{
    protected $fillable = [
        'uploaded_file_id',
        'chunk_index',
        'chunk_text',
        'embedding',
    ];

    protected $casts = [
        'chunk_index' => 'integer',
        // embedding sẽ được xử lý riêng vì là VECTOR type
    ];

    // === Relationships ===

    /**
     * File gốc chứa chunk này
     */
    public function uploadedFile(): BelongsTo
    {
        return $this->belongsTo(UploadedFile::class);
    }

    // === Helpers ===

    /**
     * Set embedding từ array float
     */
    public function setEmbeddingAttribute(array $vector): void
    {
        // Convert array thành JSON string cho MariaDB VECTOR
        $this->attributes['embedding'] = json_encode($vector);
    }

    /**
     * Get embedding dạng array float
     */
    public function getEmbeddingAttribute($value): ?array
    {
        if (is_null($value)) {
            return null;
        }
        // Nếu là string JSON thì decode
        if (is_string($value)) {
            return json_decode($value, true);
        }
        return $value;
    }
}
