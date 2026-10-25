<?php

namespace App\Exceptions;

use Exception;
use OpenAPI\Client\ApiException;

class GeocodingException extends Exception
{
    public function __construct(string $message, public readonly int $status = 500)
    {
        parent::__construct($message);
    }

    public static function zeroResults(): self
    {
        return new self('The address could not be geocoded.', 422);
    }

    public static function fromApiException(ApiException $e): self
    {
        // The geo API's own status code. Map auth/config problems to 500 (not 401/403)
        // so an upstream key problem doesn't trip the admin client's "log in again" flow.
        return match ($e->getCode()) {
            401, 403 => new self('The BMLT Geo API key is missing, invalid, or not yet approved.', 500),
            422 => new self('The geocoding request was invalid.', 422),
            429, 503 => new self('The geocoding service is temporarily unavailable. Please try again shortly.', 503),
            default => new self('There was a problem contacting the geocoding service.', 502),
        };
    }
}
