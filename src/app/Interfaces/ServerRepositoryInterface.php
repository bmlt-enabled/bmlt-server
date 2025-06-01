<?php

namespace App\Interfaces;

use App\Models\Server;
use Illuminate\Support\Collection;

interface ServerRepositoryInterface
{
    public function search(bool $eagerStatistics = false): Collection;
    public function create(array $values): Server;
    public function update(int $id, array $values): bool;
    public function delete(int $id): bool;
    public function import(Collection $externalObjects): void;
}
