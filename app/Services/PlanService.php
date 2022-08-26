<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PlanService extends BaseService
{
    public function getById($id, $strict = true)
    {
        return $strict ? Plan::find($id) : Plan::findOrFail($id);
    }

    public function create($planData)
    {
        try {
            $planData['created_by'] = Auth::guard('api')->id();

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
            $plan->update(array_filter($planData));

            return $plan;
        } catch (\PDOException $exception) {
            throw new CustomException(null, CustomException::DATABASE_LEVEL, null, 0, $exception);
        } catch (\Exception $exception) {
            throw new CustomException(null, CustomException::APP_LEVEL, null, 0, $exception);
        }
    }
}
