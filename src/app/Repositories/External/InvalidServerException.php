<?php

namespace App\Repositories\External;

class InvalidServerException extends InvalidObjectException
{
    public function __construct()
    {
        parent::__construct('Server');
    }
}
