<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'name',
        'locked',
        'payload',
    ];

    protected $casts = [
        'locked' => 'boolean',
        'payload' => 'array',
    ];

    /**
     * Common groups
     */
    const GROUP_API = 'api';
    const GROUP_APP = 'app';
    const GROUP_AI = 'ai';
    const GROUP_PAYMENT = 'payment';
    const GROUP_EMAIL = 'email';

    /**
     * Get setting value by group and name
     */
    public static function getValue(string $group, string $name, mixed $default = null): mixed
    {
        $setting = self::where('group', $group)
            ->where('name', $name)
            ->first();

        if (!$setting) {
            return $default;
        }

        return $setting->payload['value'] ?? $default;
    }

    /**
     * Set setting value
     */
    public static function setValue(string $group, string $name, mixed $value, bool $locked = false): self
    {
        return self::updateOrCreate(
            ['group' => $group, 'name' => $name],
            ['payload' => ['value' => $value], 'locked' => $locked]
        );
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->name => $setting->payload['value'] ?? null];
            })
            ->toArray();
    }

    // ============================================
    // SHORTCUT METHODS
    // ============================================

    /**
     * Get API key
     */
    public static function getApiKey(string $name, ?string $default = null): ?string
    {
        return self::getValue(self::GROUP_API, $name, $default);
    }

    /**
     * Get OpenAI API key
     */
    public static function openaiKey(): ?string
    {
        return self::getApiKey('openai_key') ?? env('OPENAI_API_KEY');
    }

    /**
     * Get Anthropic API key
     */
    public static function anthropicKey(): ?string
    {
        return self::getApiKey('anthropic_key') ?? env('ANTHROPIC_API_KEY');
    }

    /**
     * Get AI model setting
     */
    public static function aiModel(string $name, ?string $default = null): ?string
    {
        return self::getValue(self::GROUP_AI, $name, $default);
    }

    /**
     * Get default AI model
     */
    public static function defaultAiModel(): string
    {
        return self::aiModel('default_model', 'gpt-4o-mini');
    }

    /**
     * Get app setting
     */
    public static function app(string $name, mixed $default = null): mixed
    {
        return self::getValue(self::GROUP_APP, $name, $default);
    }

    /**
     * Get payment setting
     */
    public static function payment(string $name, mixed $default = null): mixed
    {
        return self::getValue(self::GROUP_PAYMENT, $name, $default);
    }

    /**
     * Check if setting is locked
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Get the actual value from payload
     */
    public function getValueAttribute(): mixed
    {
        return $this->payload['value'] ?? null;
    }
}
