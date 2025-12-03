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

        $validated = validator($request->all(), [
            '*' => function ($attribute, $value, $fail) {
                if (!array_key_exists($attribute, Setting::SETTING_TYPES)) {
                    $fail("The setting key '{$attribute}' is not valid.");
                }
            },
        ])->validate();

        $rules = [];
        foreach ($validated as $key => $value) {
            $type = Setting::SETTING_TYPES[$key];

            switch ($type) {
                case Setting::TYPE_STRING:
                    $rules[$key] = 'nullable|string|max:65535';
                    break;
                case Setting::TYPE_INT:
                    $rules[$key] = 'nullable|integer';
                    break;
                case Setting::TYPE_FLOAT:
                    $rules[$key] = 'nullable|numeric';
                    break;
                case Setting::TYPE_BOOL:
                    $rules[$key] = 'nullable|boolean';
                    break;
                case Setting::TYPE_ARRAY:
                    $rules[$key] = 'nullable|array';
                    break;
            }
        }

        $validated = validator($request->all(), $rules)->validate();
        $this->settingRepository->updateMultiple($validated);
        return response()->noContent();
    }
}
