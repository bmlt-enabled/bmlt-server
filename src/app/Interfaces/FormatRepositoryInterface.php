<?php

namespace App\Interfaces;

use App\Models\FormatTranslation;
use App\Models\FormatMain;
use App\Repositories\Import\FormatImportResult;
use Illuminate\Support\Collection;

interface FormatRepositoryInterface
{
    public function search(
        array $formatsInclude = null,
        array $formatsExclude = null,
        array $rootServersInclude = null,
        array $rootServersExclude = null,
        array $langEnums = null,
        array $keyStrings = null,
        bool $showAll = false,
        Collection $meetings = null,
        bool $eagerRootServers = false
    ): Collection;
    public function getAsTranslations(array $formatIds = null): Collection;
    public function getVirtualFormat(): FormatTranslation;
    public function getHybridFormat(): FormatTranslation;
    public function getTemporarilyClosedFormat(): FormatTranslation;
    public function create(array $sharedFormatsValues): FormatMain;
    public function update(int $sharedId, array $sharedFormatsValues): bool;
    public function delete(int $sharedId): bool;
    public function import(int $rootServerId, Collection $externalObjects): FormatImportResult;
}
