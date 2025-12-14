<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     * Returns all settings as a key-value object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function collection($resource)
    {
        $settings = [];

        foreach ($resource as $setting) {
            $settings[$setting->name] = $setting->value;
        }

        return $settings;
    }
}
