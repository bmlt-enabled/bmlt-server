<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    protected $table = 'comdef_formats';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'shared_id_bigint' => 'integer',
        ];
    }

    protected $fillable = [
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

    public function shared()
    {
        return $this->belongsTo(FormatShared::class, 'shared_id_bigint', 'shared_id_bigint');
    }

    public function translations()
    {
        return $this
            ->hasMany(self::class, 'shared_id_bigint', 'shared_id_bigint')
            ->orderBy('lang_enum');
    }

    protected function rootServerId(): Attribute
    {
        return Attribute::get(fn () => $this->shared?->root_server_id);
    }

    protected function sourceId(): Attribute
    {
        return Attribute::get(fn () => $this->shared?->source_id);
    }

    protected function worldidMixed(): Attribute
    {
        return Attribute::get(fn () => $this->shared?->worldid_mixed);
    }

    protected function iconBlob(): Attribute
    {
        return Attribute::get(fn () => $this->shared?->icon_blob);
    }

    protected function formatTypeEnum(): Attribute
    {
        return Attribute::get(fn () => $this->shared?->format_type_enum);
    }
}
