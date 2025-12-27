<?php

namespace Tests\Unit;

use App\Repositories\External\ExternalObject;

class ExternalTestObject extends ExternalObject
{
    protected function throwInvalidObjectException(): void
    {
        throw new InvalidTestObjectException();
    }

    public function validateInt(array $values, string $key): int
    {
        return parent::validateInt($values, $key);
    }

    public function validateNullableInt(array $values, string $key): ?int
    {
        return parent::validateNullableInt($values, $key);
    }

    public function validateString(array $values, string $key): string
    {
        return parent::validateString($values, $key);
    }

    public function validateNullableString(array $values, string $key): ?string
    {
        return parent::validateNullableString($values, $key);
    }

    public function validateUrl(array $values, string $key): string
    {
        return parent::validateUrl($values, $key);
    }

    public function validateTime(array $values, string $key): string
    {
        return parent::validateTime($values, $key);
    }

    public function validateNullableFloat(array $values, string $key): ?float
    {
        return parent::validateNullableFloat($values, $key);
    }

    public function validateBool(array $values, string $key): bool
    {
        return parent::validateBool($values, $key);
    }

    public function validateIntArray(array $values, string $key): array
    {
        return parent::validateIntArray($values, $key);
    }
}
