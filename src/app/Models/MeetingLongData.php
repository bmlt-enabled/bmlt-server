<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingLongData extends Model
{
    protected $table = 'comdef_meetings_longdata';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'meetingid_bigint' => 'integer',
            'visibility' => 'integer',
        ];
    }

    protected $fillable = [
        'meetingid_bigint',
        'key',
        'field_prompt',
        'lang_enum',
        'data_blob',
        'visibility',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meetingid_bigint');
    }
}
