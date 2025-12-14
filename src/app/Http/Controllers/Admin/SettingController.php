<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\SettingResource;
use App\Interfaces\SettingRepositoryInterface;
use App\FromDatabaseConfig;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends ResourceController
{
    private SettingRepositoryInterface $settingRepository;

    public function __construct(SettingRepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Setting::class);

        $settings = $this->settingRepository->getAll()->map(function ($setting) {
            $setting->value = FromDatabaseConfig::fromEnv($setting->name) ?? $setting->value;
            return $setting;
        });
        return response()->json(SettingResource::collection($settings));
    }

    public function update(Request $request)
    {
        $this->authorize('update', Setting::class);
        $settingTypes = $this->settingRepository->getAll()->mapWithKeys(fn ($s) => [$s->name => $s->type]);
        $rules = array_merge(
            ['*' => Rule::in($settingTypes->keys())],
            $settingTypes->mapWithKeys(function ($type, $name) {
                return [$name => match ($type) {
                    Setting::TYPE_STRING => 'nullable|string|max:65535',
                    Setting::TYPE_INT => 'nullable|integer',
                    Setting::TYPE_FLOAT => 'nullable|numeric',
                    Setting::TYPE_BOOL => 'nullable|boolean',
                    Setting::TYPE_ARRAY => 'nullable|array',
                }];
            })->toArray()
        );
        $validated = $request->validate($rules);
        $this->settingRepository->updateMultiple($validated);
        return response()->noContent();
    }
}
