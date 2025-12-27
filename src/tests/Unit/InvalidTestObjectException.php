<?php

namespace Tests\Unit;

use App\Repositories\External\InvalidObjectException;

class InvalidTestObjectException extends InvalidObjectException
{
    public function __construct()
    {
        parent::__construct('TestObject');
    }
}
