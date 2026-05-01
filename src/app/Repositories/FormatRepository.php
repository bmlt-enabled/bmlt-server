<?php

namespace App\Repositories;

use App\Interfaces\FormatRepositoryInterface;
use App\Models\Change;
use App\Models\Format;
use App\Models\FormatShared;
use App\Repositories\External\ExternalFormat;
use App\Repositories\Import\FormatImportResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FormatRepository implements FormatRepositoryInterface
{
    private const SHARED_KEYS = ['root_server_id', 'source_id', 'worldid_mixed', 'icon_blob', 'format_type_enum'];
    private const TRANSLATION_KEYS = ['lang_enum', 'key_string', 'name_string', 'description_string'];

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
    ): Collection {
        $formats = Format::query()->with($eagerRootServers ? ['shared.rootServer'] : ['shared']);

        if (!is_null($formatsInclude)) {
            $formats = $formats->whereIn('shared_id_bigint', $formatsInclude);
        }

        if (!is_null($formatsExclude)) {
            $formats = $formats->whereNotIn('shared_id_bigint', $formatsExclude);
        }

        if (!is_null($rootServersInclude)) {
            $formats = $formats->whereHas('shared', fn ($q) => $q->whereIn('root_server_id', $rootServersInclude));
        }

        if (!is_null($rootServersExclude)) {
            $formats = $formats->whereHas('shared', fn ($q) => $q->whereNotIn('root_server_id', $rootServersExclude));
        }

        if (!is_null($langEnums)) {
            $formats = $formats->whereIn('lang_enum', $langEnums);
        }

        if (!$showAll || !is_null($meetings)) {
            $formats = $formats->whereIn('shared_id_bigint', $this->getUsedFormatIds($meetings));
        }

        if ($keyStrings) {
            $formats = $formats->whereIn('key_string', $keyStrings);
        }

        return $formats->get();
    }

    public function getVirtualFormat(): Format
    {
        return Format::query()
            ->with('shared')
            ->where('key_string', 'VM')
            ->where('lang_enum', 'en')
            ->firstOrFail();
    }

    public function getHybridFormat(): Format
    {
        return Format::query()
            ->with('shared')
            ->where('key_string', 'HY')
            ->where('lang_enum', 'en')
            ->firstOrFail();
    }

    public function getTemporarilyClosedFormat(): Format
    {
        return Format::query()
            ->with('shared')
            ->where('key_string', 'TC')
            ->where('lang_enum', 'en')
            ->firstOrFail();
    }

    private function getUsedFormatIds(Collection $meetings = null): array
    {
        if (is_null($meetings)) {
            return DB::table('comdef_meetings_formats')
                ->select('format_shared_id_bigint')
                ->distinct()
                ->pluck('format_shared_id_bigint')
                ->all();
        }

        return $meetings
            ->flatMap(fn ($meeting) => $meeting->getFormatSharedIds())
            ->unique()
            ->values()
            ->all();
    }

    public function create(array $sharedFormatsValues): Format
    {
        return DB::transaction(function () use ($sharedFormatsValues) {
            $sharedIdBigint = (int)(FormatShared::query()->max('shared_id_bigint') ?? 0) + 1;
            $sharedValues = array_merge(
                ['shared_id_bigint' => $sharedIdBigint],
                $this->extractSharedValues($sharedFormatsValues),
            );
            FormatShared::create($sharedValues);

            $format = null;
            foreach ($sharedFormatsValues as $values) {
                $format = Format::create(array_merge(
                    $this->extractTranslationValues($values),
                    ['shared_id_bigint' => $sharedIdBigint],
                ));
                $format->setRelation('shared', FormatShared::find($sharedIdBigint));
                if (!file_config('aggregator_mode_enabled')) {
                    $this->saveChange(null, $format);
                }
            }
            return $format;
        });
    }

    public function update(int $sharedId, array $sharedFormatsValues): bool
    {
        return DB::transaction(function () use ($sharedId, $sharedFormatsValues) {
            $shared = FormatShared::query()->where('shared_id_bigint', $sharedId)->firstOrFail();
            $shared->fill($this->extractSharedValues($sharedFormatsValues));
            $shared->save();

            $oldFormats = Format::query()
                ->with('shared')
                ->where('shared_id_bigint', $sharedId)
                ->get()
                ->mapWithKeys(fn ($fmt, $_) => [$fmt->lang_enum => $fmt]);

            Format::query()->where('shared_id_bigint', $sharedId)->delete();

            // save changes for deleted formats
            foreach ($oldFormats as $oldFormat) {
                $isDeleted = collect($sharedFormatsValues)
                    ->filter(fn ($values) => $values['lang_enum'] == $oldFormat->lang_enum)
                    ->isEmpty();
                if ($isDeleted) {
                    if (!file_config('aggregator_mode_enabled')) {
                        $this->saveChange($oldFormat, null);
                    }
                }
            }

            foreach ($sharedFormatsValues as $values) {
                $newFormat = Format::create(array_merge(
                    $this->extractTranslationValues($values),
                    ['shared_id_bigint' => $sharedId],
                ));
                $newFormat->setRelation('shared', $shared);
                $oldFormat = $oldFormats->get($newFormat->lang_enum);
                if (!file_config('aggregator_mode_enabled')) {
                    if (is_null($oldFormat)) {
                        $this->saveChange(null, $newFormat);
                    } else {
                        $this->saveChange($oldFormat, $newFormat);
                    }
                }
            }

            return true;
        });
    }

    public function delete(int $sharedId): bool
    {
        return DB::transaction(function () use ($sharedId) {
            $formats = Format::query()
                ->with('shared')
                ->where('shared_id_bigint', $sharedId)
                ->get();
            if ($formats->isEmpty()) {
                return false;
            }

            // Cascade through FormatShared deletes the translations and the
            // pivot rows. We capture the translations first so we can record
            // per-language change records.
            FormatShared::query()->where('shared_id_bigint', $sharedId)->delete();

            foreach ($formats as $format) {
                if (!file_config('aggregator_mode_enabled')) {
                    $this->saveChange($format, null);
                }
            }
            return true;
        });
    }

    public function getAsTranslations(array $formatIds = null): Collection
    {
        return Format::query()
            ->with(['shared', 'translations'])
            ->whereIn('id', function ($query) use ($formatIds) {
                $query->selectRaw(DB::raw('MIN(id)'));
                $query->from('comdef_formats');
                if (!is_null($formatIds)) {
                    $query->whereIn('shared_id_bigint', $formatIds);
                }
                $query->groupBy('shared_id_bigint');
            })
            ->get();
    }

    private function saveChange(?Format $beforeFormat, ?Format $afterFormat): void
    {
        $beforeObject = !is_null($beforeFormat) ? $this->serializeForChange($beforeFormat) : null;
        $afterObject = !is_null($afterFormat) ? $this->serializeForChange($afterFormat) : null;
        if (!is_null($beforeObject) && !is_null($afterObject) && $beforeObject == $afterObject) {
            // nothing actually changed, don't save a record
            return;
        }

        Change::create([
            'user_id_bigint' => request()->user()->id_bigint,
            'service_body_id_bigint' => $afterFormat?->shared_id_bigint ?? $beforeFormat->shared_id_bigint,
            'lang_enum' => $beforeFormat?->lang_enum ?: $afterFormat?->lang_enum,
            'object_class_string' => 'c_comdef_format',
            'before_id_bigint' => $beforeFormat?->shared_id_bigint,
            'before_lang_enum' => $beforeFormat?->lang_enum,
            'after_id_bigint' => $afterFormat?->shared_id_bigint,
            'after_lang_enum' => $afterFormat?->lang_enum,
            'change_type_enum' => is_null($beforeFormat) ? 'comdef_change_type_new' : (is_null($afterFormat) ? 'comdef_change_type_delete' : 'comdef_change_type_change'),
            'before_object' => $beforeObject,
            'after_object' => $afterObject,
        ]);
    }

    private function serializeForChange(Format $format): string
    {
        return serialize([
            $format->shared_id_bigint,
            $format->format_type_enum,
            $format->key_string,
            $format->icon_blob,
            $format->worldid_mixed,
            $format->lang_enum,
            $format->name_string,
            $format->description_string,
        ]);
    }

    public function import(int $rootServerId, Collection $externalObjects): FormatImportResult
    {
        $result = new FormatImportResult();

        $sourceIds = $externalObjects->pluck('id');

        // deleted formats: kill the FormatShared rows for source_ids that are
        // no longer present, which cascades into translations and pivot rows.
        $deletedSharedIds = FormatShared::query()
            ->where('root_server_id', $rootServerId)
            ->whereNotIn('source_id', $sourceIds)
            ->pluck('shared_id_bigint');
        if ($deletedSharedIds->isNotEmpty()) {
            $result->numDeleted = Format::query()->whereIn('shared_id_bigint', $deletedSharedIds)->count();
            FormatShared::query()->whereIn('shared_id_bigint', $deletedSharedIds)->delete();
        }

        $bySourceIdByLanguage = $externalObjects->groupBy(['id', 'language']);
        foreach ($bySourceIdByLanguage as $sourceId => $byLanguage) {
            $languages = $byLanguage->keys();

            $sharedIds = FormatShared::query()
                ->where('root_server_id', $rootServerId)
                ->where('source_id', $sourceId)
                ->pluck('shared_id_bigint');

            // deleted languages
            $result->numDeleted += Format::query()
                ->whereIn('shared_id_bigint', $sharedIds)
                ->whereNotIn('lang_enum', $languages)
                ->delete();

            $existingFormats = Format::query()
                ->with('shared')
                ->whereIn('shared_id_bigint', $sharedIds)
                ->get();

            $externalFormats = $byLanguage->map(fn ($f) => $f->first());

            if ($existingFormats->isEmpty()) {
                $values = $this->externalFormatToValuesArray($rootServerId, $sourceId, $externalFormats);
                $this->create($values);
                $result->numCreated++;
            } else {
                $isDirty = $existingFormats->count() != $externalFormats->count();
                if (!$isDirty) {
                    foreach ($externalFormats as $externalFormat) {
                        $dbFormat = $existingFormats->where('lang_enum', $externalFormat->language)->first();
                        $isDirty = is_null($dbFormat) || !$externalFormat->isEqual($dbFormat);
                        if ($isDirty) {
                            break;
                        }
                    }
                }

                if ($isDirty) {
                    $existingSharedId = $existingFormats->first()->shared_id_bigint;
                    $values = $this->externalFormatToValuesArray($rootServerId, $sourceId, $externalFormats);
                    $this->update($existingSharedId, $values);
                    $result->numUpdated++;
                }
            }
        }

        return $result;
    }

    private function externalFormatToValuesArray(int $rootServerId, int $sourceId, Collection $externalFormats): array
    {
        return $externalFormats
            ->map(fn (ExternalFormat $f) => [
                'root_server_id' => $rootServerId,
                'source_id' => $sourceId,
                'key_string' => $f->key,
                'name_string' => $f->name,
                'description_string' => $f->description,
                'lang_enum' => $f->language,
                'format_type_enum' => $f->type,
                'worldid_mixed' => $f->worldId,
            ])
            ->values()
            ->toArray();
    }

    private function extractSharedValues(array $sharedFormatsValues): array
    {
        $first = collect($sharedFormatsValues)->first() ?? [];
        return array_intersect_key($first, array_flip(self::SHARED_KEYS));
    }

    private function extractTranslationValues(array $values): array
    {
        return array_intersect_key($values, array_flip(self::TRANSLATION_KEYS));
    }
}
