<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

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
		$setting = DB::table('settings')
            ->select(['value'])
            ->where('name',$name)
            ->first();
		return $setting?->value;
    }

    public function updateSettingByName(
        string $setting,
        string|int|bool|null $value
    ): void {
        try {
            Setting::where('name', $setting)->update([
                'value' => $value
            ]);
        } catch (Exception $e) {
            dd('Failed to update setting - ' . $e->getMessage());
        }
    }
}
