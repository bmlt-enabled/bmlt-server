<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table = 'servers';
    protected $fillable = [
        'source_id',
        'name',
        'url',
        'server_info',
    ];

    public function statistics()
    {
        return $this->hasMany(ServerStatistics::class);
    }
}
