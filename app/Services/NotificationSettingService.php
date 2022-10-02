<?php

namespace App\Services;

use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Auth;

class NotificationSettingService extends BaseService
{
    /**
     * @param $id
     * @param bool $strict
     * @return NotificationSetting
     */
    public function getById($id, $strict = true)
    {
        return $strict ? NotificationSetting::find($id) : NotificationSetting::findOrFail($id);;
    }

    public function update($settingData)
    {
        $notificationSettingQuery = Auth::guard('api')->user()->notificationSetting();
        $notificationSetting = $notificationSettingQuery->get();

        if (empty($notificationSetting->id)) {
            $settingData['user_id'] = Auth::guard('api')->id();
            $notificationSetting = NotificationSetting::create($settingData);
        } else {
            Auth::guard('api')->user()->notificationSetting()->update($settingData);
            $notificationSetting = $notificationSetting->fresh();
        }

        return $notificationSetting;
    }
}
