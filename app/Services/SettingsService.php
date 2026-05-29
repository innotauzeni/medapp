<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'er.site_settings';
    private const CACHE_TTL = 600; // 10 minutes

    /** @return array<string, mixed> */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return SiteSetting::all()
                ->mapWithKeys(fn (SiteSetting $s) => [$s->key => $s->cast_value])
                ->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function set(string $key, mixed $value): SiteSetting
    {
        /** @var SiteSetting $row */
        $row = SiteSetting::firstOrNew(['key' => $key]);
        $type = $row->type ?: 'string';

        $stored = match ($type) {
            'bool' => $value ? '1' : '0',
            'int'  => (string) (int) $value,
            'json' => json_encode($value),
            default => (string) $value,
        };

        $row->value = $stored;
        $row->save();

        $this->clear();
        return $row;
    }

    public function setMany(array $kv): void
    {
        foreach ($kv as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function clear(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
