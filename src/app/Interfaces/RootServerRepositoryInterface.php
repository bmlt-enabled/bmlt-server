<?php

namespace App\Interfaces;

use App\Models\RootServer;
use App\Repositories\Import\RootServerImportResult;
use Illuminate\Support\Collection;

interface RootServerRepositoryInterface
{
    public function search(bool $eagerStatistics = false): Collection;
    public function create(array $values): RootServer;
    public function update(int $id, array $values): bool;
    public function delete(int $id): bool;
    public function import(Collection $externalObjects): RootServerImportResult;
}
