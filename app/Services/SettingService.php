<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SettingService
{
    /**
     * Get a setting value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }

        $setting = Setting::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Set a setting value.
     */
    public function set(
        string $key,
        mixed $value,
        string $group = 'general',
        ?string $description = null,
        bool $isPublic = false
    ): Setting {
        return Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );
    }

    /**
     * Get all settings.
     */
    public function all(): Collection
    {
        return Setting::all();
    }

    /**
     * Get settings by group.
     */
    public function group(string $group): Collection
    {
        return Setting::where('group', $group)->get();
    }

    /**
     * Check if a setting exists.
     */
    public function has(string $key): bool
    {
        return Setting::where('key', $key)->exists();
    }

    /**
     * Delete a setting.
     */
    public function forget(string $key): bool
    {
        return Setting::where('key', $key)->delete() > 0;
    }
}
