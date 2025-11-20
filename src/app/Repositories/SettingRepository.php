<?php

namespace App\Repositories;

use App\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SettingRepository implements SettingRepositoryInterface
{
    public function getByKey(string $key): ?Setting
    {
        return Setting::query()->where('name', $key)->first();
    }

    public function getAll(): Collection
    {
        $settings = Setting::all();

        // Apply environment variable overrides
        return $settings->map(function ($setting) {
            $value = Setting::get($setting->name);
            if ($value !== $setting->value) {
                $setting->value = $value;
            }
            return $setting;
        });
    }

    public function update(string $key, $value): bool
    {
        $setting = Setting::updateOrCreate(
            ['name' => $key],
            ['value' => $value]
        );

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    public function updateMultiple(array $keyValuePairs): bool
    {
        return DB::transaction(function () use ($keyValuePairs) {
            $success = true;

            foreach ($keyValuePairs as $key => $value) {
                if (!$this->update($key, $value)) {
                    $success = false;
                }
            }

            return $success;
        });
    }
}
