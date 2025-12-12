<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\SettingResource;
use App\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Http\Request;

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

        $settings = $this->settingRepository->getAll();
        return response()->json(SettingResource::collection($settings));
    }

    public function update(Request $request)
    {
        $this->authorize('update', Setting::class);

        // only known settings keys are allowed
        $rules = [];
        foreach (Setting::SETTING_TYPES as $key => $type) {
            $rules[$key] = match ($type) {
                Setting::TYPE_STRING => 'nullable|string|max:65535',
                Setting::TYPE_INT => 'nullable|integer',
                Setting::TYPE_FLOAT => 'nullable|numeric',
                Setting::TYPE_BOOL => 'nullable|boolean',
                Setting::TYPE_ARRAY => 'nullable|array',
            };
        }

        $validated = validator($request->all(), $rules)->validate();
        $this->settingRepository->updateMultiple($validated);
        return response()->noContent();
    }
}
