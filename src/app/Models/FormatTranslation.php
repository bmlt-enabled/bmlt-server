<?php

namespace App\Models;

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
    public function main()
    {
        return $this->belongsTo(FormatMain::class, 'shared_id_bigint', 'shared_id_bigint');
    }
    //We want these to emulate the DB field names as in the old schema
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function format_type_enum()
    {
        return $this->main()->first()->format_type_enum;
    }
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function worldid_mixed()
    {
        return $this->main()->first()->worldid_mixed;
    }
}
