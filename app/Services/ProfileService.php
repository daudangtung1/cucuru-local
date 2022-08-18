<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileService extends BaseService
{
    public function getById($id, $strict = true)
    {
        $user = $strict ? Profile::find($id) : Profile::findOrFail($id);

        return $user;
    }

    public function updateProfile($profileData)
    {
        try {
            $user = Auth::guard('api')->user();
            $profile = $user->profile();
            if (empty($profile->get()->id)) {
                $profile = $profile->create(['user_id' => Auth::guard('api')->id()]);
            }

            if (!empty($profileData['username'])) {
                $user->update(['username' => $profileData['username']]);
            }
            return $profile->update($profileData);
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }
}
