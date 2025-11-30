<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FormatMain extends Model
{
    protected $table = 'comdef_formats_main';
    protected $primaryKey = 'shared_id_bigint';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'root_server_id',
        'source_id',
        'worldid_mixed',
        'format_type_enum',
    ];

    public function getRouteKeyName()
    {
        return 'shared_id_bigint';
    }

    public function rootServer()
    {
        return $this->belongsTo(RootServer::class, 'root_server_id');
    }
    public function translations()
    {
        return $this->hasMany(FormatTranslation::class, 'shared_id_bigint', 'shared_id_bigint');
    }
}
