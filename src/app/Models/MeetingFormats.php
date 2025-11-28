<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MeetingFormats extends Model
{
    protected $table = 'comdef_meeting_formats';
    public $timestamps = false;
    protected $fillable = [
        'meeting_id',
        'format_id',
    ];
}
