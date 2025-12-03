<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\JsonResource;
use App\Models\FormatType;

class FormatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $main = (get_class($this->resource) != "App\Models\FormatMain") ? $this->resource->main : $this->resource;
        return [
            'id' => $main->shared_id_bigint,
            'worldId' => $main->worldid_mixed,
            'type' => FormatType::getApiEnumFromKey($main->format_type_enum),
            'translations' => $main->translations->map(function ($translation) {
                return [
                    'key' => $translation->key_string ?? '',
                    'name' => $translation->name_string ?? '',
                    'description' => $translation->description_string ?? '',
                    'language' => $translation->lang_enum,
                ];
            })
        ];
    }
}
