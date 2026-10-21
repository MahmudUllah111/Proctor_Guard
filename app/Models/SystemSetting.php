<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'is_secret',
    ];

    protected $casts = [
        'is_secret' => 'boolean',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'json' => json_decode($setting->value, true) ?? $default,
                default => $setting->value ?? $default,
            };
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string', bool $isSecret = false): static
    {
        $encoded = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $encoded,
                'group' => $group,
                'type' => $type,
                'is_secret' => $isSecret,
            ]
        );

        Cache::forget("setting_{$key}");
        Cache::forget("settings_group_{$group}");

        return $setting;
    }

    public static function getGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            $settings = static::where('group', $group)->get();
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = match ($setting->type) {
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $setting->value,
                    'json' => json_decode($setting->value, true),
                    default => $setting->value,
                };
            }
            return $result;
        });
    }
}
