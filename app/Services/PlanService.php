<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PlanService extends BaseService
{
    const LIMIT_RECORD = 4;
    const LIMIT_FREE_PLAN = 1;

    public function getById($id, $strict = true)
    {
        return $strict ? Plan::find($id) : Plan::findOrFail($id);
    }

    public function create($planData)
    {
        try {
            $plans = Auth::guard('api')->user()->plans;

            if (count($plans) >= 4) {
                return ['error' => __('plan.message.greater_limit_plan', ['limit' => self::LIMIT_RECORD])];
            } elseif ($planData['monthly_fee'] == 0 && $plans->filter(function ($plan) {
                return $plan->monthly_fee == 0;
            })->count() >= self::LIMIT_FREE_PLAN) {
                return ['error' => __('plan.message.greater_limit_free_plan', ['limit' => self::LIMIT_FREE_PLAN])];
            }

            $planData['user_id'] = Auth::guard('api')->id();

            return Plan::create($planData);
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }

    public function update($plan, $planData)
    {
        try {
            $plans = Auth::guard('api')->user()->plans;

            if (isset($planData['monthly_fee']) && $planData['monthly_fee'] == 0 && $plans->filter(function ($plan) {
                return $plan->monthly_fee == 0;
            })->count() >= self::LIMIT_FREE_PLAN) {
                return ['error' => __('plan.message.greater_limit_free_plan', ['limit' => self::LIMIT_FREE_PLAN])];
            }

            $plan->update(array_filter($planData));

            return $plan;
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }
}
