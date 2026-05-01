<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatShared extends Model
{
    protected $table = 'comdef_format_shared';
    protected $primaryKey = 'shared_id_bigint';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'shared_id_bigint' => 'integer',
            'root_server_id' => 'integer',
            'source_id' => 'integer',
        ];
    }

    protected $fillable = [
        'shared_id_bigint',
        'root_server_id',
        'source_id',
        'worldid_mixed',
        'icon_blob',
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
        return $this
            ->hasMany(Format::class, 'shared_id_bigint', 'shared_id_bigint')
            ->orderBy('lang_enum');
    }

    public function meetings()
    {
        return $this->belongsToMany(
            Meeting::class,
            'comdef_meetings_formats',
            'format_shared_id_bigint',
            'meeting_id_bigint',
            'shared_id_bigint',
            'id_bigint',
        );
    }
}
