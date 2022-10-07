<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Affiliate;
use Illuminate\Support\Facades\Auth;

class AffiliateService extends BaseService
{
    public function getById($id, $strict = true)
    {
        $user = $strict ? Affiliate::find($id) : Affiliate::findOrFail($id);

        return $user;
    }

    public function register($data)
    {
        try {
            $user = Auth::guard('api')->user();
            $affiliate = $user->affiliate;
            if (!empty($affiliate)) {
                return ['error' => __('affiliate.has_been_registered')];
            }

            $data['user_id'] = Auth::guard('api')->id();
            $data['affiliate_link'] = 'cucuru_' . $data['user_id'];

            return Affiliate::create($data);
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }
}
