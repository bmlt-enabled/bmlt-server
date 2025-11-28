<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FormatTranslation extends Model
{
    protected $table = 'comdef_formats_translations';
    public $timestamps = false;
    protected $fillable = [
        'root_server_id',
        'source_id',
        'shared_id_bigint',
        'key_string',
        'lang_enum',
        'name_string',
        'description_string',
    ];

    public function getRouteKeyName()
    {
        return 'shared_id_bigint';
    }

    public function rootServer()
    {
        return $this->belongsTo(RootServer::class, 'root_server_id');
    }
}
