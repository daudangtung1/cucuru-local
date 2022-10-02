<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    /**
     * @var ProfileService
     */
    protected $profileService;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->profileService = new ProfileService();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            if (!$this->customValidate($request, [
                'username' => 'sometimes|max:30',
                'gender' => 'sometimes|in:' .implode(',', Profile::GENDER),
                'birth_day' => 'sometimes|date_format:Y/m/d',
                'twitter_url' => 'sometimes|url',
                'instagram_url' => 'sometimes|url',
                'tiktok_url' => 'sometimes|url',
                'youtube_url' => 'sometimes|url',
                'amazon_url' => 'sometimes|url',
                'facebook_url' => 'sometimes|url',
                'allow_set_post_sensitive_content' => 'sometimes|in:0,1',
                'allow_view_post_sensitive_content' => 'sometimes|in:0,1',
                'full_name' => 'sometimes|min:1',
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $profileData = $request->only('full_name', 'gender', 'birth_day', 'profile_info', 'username',
                'twitter_url', 'instagram_url', 'tiktok_url', 'youtube_url', 'amazon_url', 'facebook_url',
                'allow_set_post_sensitive_content', 'allow_view_post_sensitive_content'
            );

            $this->transactionStart();
            $profile = $this->profileService->updateProfile($profileData);

            if (isset($profile['errors'])) {
                return $this->responseFail($profile['errors']);
            }

            return $this->responseSuccess($profile, trans('profile.message.update_profile_success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }
}
