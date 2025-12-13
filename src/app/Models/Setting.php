<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['name', 'type', 'value'];

    public const TYPE_STRING = 'string';
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOL = 'bool';
    public const TYPE_ARRAY = 'array';

    public const SETTING_TYPES = [
        'googleApiKey' => self::TYPE_STRING,
        'changeDepthForMeetings' => self::TYPE_INT,
        'defaultSortKey' => self::TYPE_STRING,
        'language' => self::TYPE_STRING,
        'defaultDurationTime' => self::TYPE_STRING,
        'regionBias' => self::TYPE_STRING,
        'distanceUnits' => self::TYPE_STRING,
        'meetingStatesAndProvinces' => self::TYPE_ARRAY,
        'meetingCountiesAndSubProvinces' => self::TYPE_ARRAY,
        'searchSpecMapCenterLongitude' => self::TYPE_FLOAT,
        'searchSpecMapCenterLatitude' => self::TYPE_FLOAT,
        'searchSpecMapCenterZoom' => self::TYPE_INT,
        'numberOfMeetingsForAuto' => self::TYPE_INT,
        'autoGeocodingEnabled' => self::TYPE_BOOL,
        'countyAutoGeocodingEnabled' => self::TYPE_BOOL,
        'zipAutoGeocodingEnabled' => self::TYPE_BOOL,
        'defaultClosedStatus' => self::TYPE_BOOL,
        'enableLanguageSelector' => self::TYPE_BOOL,
        'includeServiceBodyEmailInSemantic' => self::TYPE_BOOL,
        'bmltTitle' => self::TYPE_STRING,
        'bmltNotice' => self::TYPE_STRING,
        'formatLangNames' => self::TYPE_ARRAY,
    ];

    /**
     * Automatically JSON encodes/decodes and converts types.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_null($value) ? null : json_decode($value, true),
            set: function ($value) {
                if (is_null($value)) {
                    return null;
                }

                $type = $this->type ?? self::SETTING_TYPES[$this->name] ?? self::TYPE_STRING;
                $value = $this->convertToType($value, $type);

                return json_encode($value);
            },
        );
    }

    /**
     * Convert a value to the specified type.
     */
    private function convertToType($value, string $type)
    {
        switch ($type) {
            case self::TYPE_ARRAY:
                if (is_array($value)) {
                    return $value;
                }
                if (is_string($value)) {
                    return $value === '' ? [] : array_map('trim', explode(',', $value));
                }
                return [];

            case self::TYPE_BOOL:
                return (bool) $value;

            case self::TYPE_INT:
                return (int) $value;

            case self::TYPE_FLOAT:
                return (float) $value;

            case self::TYPE_STRING:
            default:
                return (string) $value;
        }
    }
}
