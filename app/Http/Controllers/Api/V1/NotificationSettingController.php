<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Services\NotificationSettingService;
use Illuminate\Http\Request;

class NotificationSettingController extends ApiController
{
    /**
     * @var NotificationSettingService
     */
    protected $notificationSettingService;

    /**
     * NotificationSettingController constructor.
     */

    public function __construct()
    {
        parent::__construct();
        $this->notificationSettingService = new NotificationSettingService();
    }

    public function update(Request $request)
    {
        if (!$this->customValidate($request, [
            'email_notification' => 'sometimes|in:0,1',
            'comment_notification' => 'sometimes|in:0,1',
            'reply_notification' => 'sometimes|in:0,1',
            'follow_notification' => 'sometimes|in:0,1',
            'join_fan_club_notification' => 'sometimes|in:0,1',
            'tip_notification' => 'sometimes|in:0,1',
            'post_video_compression_notification' => 'sometimes|in:0,1',
            'following_creator_post_notification' => 'sometimes|in:0,1',
            'subscribing_creator_post_notification' => 'sometimes|in:0,1',
            'in_site_notification' => 'sometimes|in:0,1',
            'cancel_of_plan_notification' => 'sometimes|in:0,1',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $settingData = $request->only([
            'email_notification',
            'comment_notification',
            'reply_notification',
            'follow_notification',
            'join_fan_club_notification',
            'tip_notification',
            'post_video_compression_notification',
            'following_creator_post_notification',
            'subscribing_creator_post_notification',
            'in_site_notification',
            'cancel_of_plan_notification',
        ]);

        if (empty($settingData)) {
            return $this->responseFail(trans('setting.message.no_data_update'));
        }

        $resultUpdate = $this->notificationSettingService->update($settingData);

        return $this->responseSuccess($resultUpdate);
    }
}
