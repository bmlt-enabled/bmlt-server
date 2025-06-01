<?php

namespace App\Repositories\External;

use App\Models\Server;

class ExternalServer extends ExternalObject
{
    public int $id;
    public string $name;
    public string $url;

    public function __construct(array $values)
    {
        $this->id = $this->validateInt($values, 'id');
        $this->name = $this->validateString($values, 'name');
        $this->url = $this->validateUrl($values, 'url');
    }

    public function isEqual(Server $server): bool
    {
        if ($this->id != $server->source_id) {
            return false;
        }
        if ($this->name != $server->name) {
            return false;
        }
        if ($this->url != $server->url) {
            return false;
        }
        return true;
    }

    protected function throwInvalidObjectException(): void
    {
        throw new InvalidServerException();
    }
}
