<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandAgent extends Model
{
    protected $table = 'brand_agent';

    protected $fillable = [
        'brand_id',
        'code',
        'name',
        'target',
        'output_description',
        'assistant_id',
        'assistant_key',
        'prompt',
        'ui_display',
        'step_order',
        'instruction',
        'vector_id',
        'is_include',
        'created_by'
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    protected $casts = [
        'is_include' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * File đã upload cho agent này (RAG)
     */
    public function uploadedFiles()
    {
        return $this->morphMany(UploadedFile::class, 'attachable');
    }
}
