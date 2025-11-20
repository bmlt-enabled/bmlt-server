<?php

namespace App\Rules;

use App\Interfaces\FormatRepositoryInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FormatTranslations implements ValidationRule
{
    private FormatRepositoryInterface $formatRepository;
    private ?int $formatId;

    public function __construct(FormatRepositoryInterface $formatRepository, ?int $formatId)
    {
        $this->formatRepository = $formatRepository;
        $this->formatId = $formatId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($this->formatId)) {
            return;
        }

        $virtualFormatId = $this->formatRepository->getVirtualFormat()->shared_id_bigint;
        $hybridFormatId = $this->formatRepository->getHybridFormat()->shared_id_bigint;
        $tempClosedFormatId = $this->formatRepository->getTemporarilyClosedFormat()->shared_id_bigint;
        $reservedFormatIds = [$virtualFormatId, $hybridFormatId, $tempClosedFormatId];
        if (!in_array($this->formatId, $reservedFormatIds)) {
            return;
        }

        $translations = collect($value);
        if ($translations->filter(fn ($t) => $t['language'] == 'en')->isEmpty()) {
            $fail("the english translation of a reserved format cannot be deleted.");
        }
    }
}
