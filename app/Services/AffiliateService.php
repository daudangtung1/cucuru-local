<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Affiliate;
use Illuminate\Support\Facades\Auth;
use Str;

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
            $data['code'] = $this->genAffiliateCode();

            if (is_null($data['code'])) {
                return ['error' => __('affiliate.something_wrong_when_gen_code')];
            }

            return Affiliate::create($data);
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }

    protected function genAffiliateCode(&$retry = 1)
    {
        $affiliateCode = substr(str_shuffle(str_repeat('0123456789', 5)), 0, 6);

        if (!Affiliate::where('code', $affiliateCode)->first()) {
            return $affiliateCode;
        } else {
            $retry ++;
            if ($retry > 10) {
                return null;
            }
            $this->genAffiliateCode();
        }
    }
}
