<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Setting;

class SettingService
{
    public function __construct(
    )
    {

    }

    public function getSettingByName(
        ?string $name
    ): string {
        return Setting::firstWhere('name', $name)->value;
    }

    public function updateSettingByName(
        string $setting,
        string|int|null $value
    ): bool {

        // update here...
    }
}
