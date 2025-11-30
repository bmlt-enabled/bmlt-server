<?php

namespace App\Interfaces;

use App\Models\Setting;
use Illuminate\Support\Collection;

interface SettingRepositoryInterface
{
    public function getByKey(string $key): ?Setting;
    public function getAll(): Collection;
    public function update(string $key, $value): bool;
    public function updateMultiple(array $keyValuePairs): bool;
}
