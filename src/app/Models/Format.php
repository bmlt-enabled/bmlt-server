<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    protected $table = 'comdef_formats';
    protected $fillable = [
        'root_server_id',
        'source_id',
        'shared_id_bigint',
        'key_string',
        'worldid_mixed',
        'lang_enum',
        'name_string',
        'description_string',
        'format_type_enum',
    ];
    public function meetings()
    {
        $formatId = $this->attributes['shared_id_bigint'];
        return MeetingFormats::query()->where('format_id', $formatId)->get();
    }
}
