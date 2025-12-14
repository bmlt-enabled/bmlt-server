<?php

namespace App\Interfaces;

use App\Models\Setting;
use Illuminate\Support\Collection;

interface SettingRepositoryInterface
{
    public function getByName(string $name): ?Setting;
    public function getAll(): Collection;
    public function update(string $key, $value): bool;
    public function updateMultiple(array $keyValuePairs): bool;
}
