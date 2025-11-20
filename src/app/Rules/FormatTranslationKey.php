<?php

namespace App\Rules;

use App\Interfaces\FormatRepositoryInterface;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class FormatTranslationKey implements DataAwareRule, ValidationRule
{
    protected $data = [];
    private FormatRepositoryInterface $formatRepository;
    private ?int $formatId;

    public function __construct(FormatRepositoryInterface $formatRepository, ?int $formatId)
    {
        $this->formatRepository = $formatRepository;
        $this->formatId = $formatId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $translation = $this->data['translations'][intval(explode('.', $attribute)[1])];
        if (!isset($translation['language'])) {
            // we can trust that another validator will fail this
            return;
        }

        if ($translation['language'] == 'en') {
            if (is_null($this->formatId)) {
                if ($value == 'VM' || $value == 'HY' || $value == 'TC') {
                    $fail(':attribute cannot be VM, HY, or TC for the english translation.');
                }
            } else {
                $existingFormat = $this->formatRepository->search(formatsInclude: [$this->formatId], langEnums: ['en'], showAll: true)->first();
                if (is_null($existingFormat)) {
                    if ($value == 'VM' || $value == 'HY' || $value == 'TC') {
                        $fail(':attribute cannot be VM, HY, or TC for the english translation.');
                    }
                } else {
                    if ($existingFormat->key_string == 'VM' && $value != 'VM') {
                        $fail(':attribute cannot be changed for the english VM format.');
                    } elseif ($existingFormat->key_string == 'HY' && $value != 'HY') {
                        $fail(':attribute cannot be changed for the english HY format.');
                    } elseif ($existingFormat->key_string == 'TC' && $value != 'TC') {
                        $fail(':attribute cannot be changed for the english TC format.');
                    }
                }
            }
        }

        $duplicates = $this->formatRepository->search(
            formatsExclude: is_null($this->formatId) ? null : [$this->formatId],
            langEnums: [$translation['language']],
            keyStrings: [$value],
            showAll: true
        );
        if ($duplicates->isNotEmpty()) {
            $fail(':attribute cannot be the same as another format\'s for the same language.');
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
}
