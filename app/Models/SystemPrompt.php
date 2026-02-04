<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemPrompt extends Model
{
    protected $table = 'system_prompts';

    protected $fillable = [
        'short_code',
        'prompt',
        'description',
    ];

    /**
     * Lấy prompt theo short_code
     * Có cache để tránh query nhiều lần
     * 
     * @param string $shortCode
     * @return string|null
     */
    public static function getPrompt(string $shortCode): ?string
    {
        static $cache = [];

        if (!isset($cache[$shortCode])) {
            $systemPrompt = self::where('short_code', $shortCode)->first();
            $cache[$shortCode] = $systemPrompt?->prompt;
        }

        return $cache[$shortCode];
    }

    /**
     * Lấy prompt với fallback nếu không tìm thấy
     * 
     * @param string $shortCode
     * @param string $fallback
     * @return string
     */
    public static function getPromptOrDefault(string $shortCode, string $fallback = ''): string
    {
        return self::getPrompt($shortCode) ?? $fallback;
    }
}
