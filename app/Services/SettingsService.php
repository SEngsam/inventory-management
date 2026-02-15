<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        if (!$all->has($key)) {
            return $default;
        }

        return $this->decodeIfJson($all->get($key));
    }

    public function set(string $key, mixed $value): void
    {
        $raw = is_array($value) ? json_encode($value) : (is_null($value) ? null : (string) $value);

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $raw]
        );

        $this->forgetCache();
    }

    public function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            $raw = is_array($value) ? json_encode($value) : (is_null($value) ? null : (string) $value);

            Setting::updateOrCreate(
                ['key' => (string) $key],
                ['value' => $raw]
            );
        }

        $this->forgetCache();
    }

    public function forget(string $key): void
    {
        Setting::where('key', $key)->delete();
        $this->forgetCache();
    }

    /**
     * @return \Illuminate\Support\Collection<string, string|null>
     */
    public function all(): Collection
    {
        $cacheKey = config('settings.cache_key');
        $store = config('settings.cache_store');

        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->rememberForever($cacheKey, function () {
            return Setting::query()
                ->pluck('value', 'key'); // Collection: key => value
        });
    }

    public function forgetCache(): void
    {
        $cacheKey = config('settings.cache_key');
        $store = config('settings.cache_store');
        $cache = $store ? Cache::store($store) : Cache::store();

        $cache->forget($cacheKey);
    }

    private function decodeIfJson(?string $value): mixed
    {
        if ($value === null) return null;

        $trim = ltrim($value);
        if ($trim === '' ) return $value;

        // decode arrays/objects stored as json
        if (str_starts_with($trim, '{') || str_starts_with($trim, '[')) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $value;
    }
}
