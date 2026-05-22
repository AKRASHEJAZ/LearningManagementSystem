<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsService
{
    private const CACHE_KEY = 'settings.all.v1';

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if (! Schema::hasTable('settings')) {
            return [];
        }

        /** @var array<string, mixed> $settings */
        $settings = Cache::rememberForever(self::CACHE_KEY, function (): array {
            return Setting::query()
                ->get(['key', 'value', 'type'])
                ->mapWithKeys(function (Setting $setting): array {
                    return [$setting->key => $this->castValue($setting->value, $setting->type)];
                })
                ->all();
        });

        return $settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->all(), $key, $default);
    }

    /**
     * @param array<string, mixed> $values
     */
    public function setMany(array $values, string $group = 'general', bool $isPublic = false): void
    {
        foreach ($values as $key => $value) {
            $type = $this->inferType($value);

            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $this->serializeValue($value, $type),
                    'type' => $type,
                    'group' => $group,
                    'is_public' => $isPublic,
                ]
            );
        }

        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function inferType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'bool',
            is_array($value) => 'json',
            default => 'text',
        };
    }

    private function serializeValue(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'bool' => $value ? '1' : '0',
            'json' => json_encode($value, JSON_THROW_ON_ERROR),
            default => (string) $value,
        };
    }

    private function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'bool' => $value === '1',
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
