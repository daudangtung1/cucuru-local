<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;
use App\Services\PlanService;
use App\Utils\AppConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class PlanController extends ApiController
{
    /**
     * @var PlanService
     */
    protected $planService;

    /**
     * PlanController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->planService = new PlanService();
    }

    public function store(Request $request)
    {
        try {
            if (!$this->customValidate($request, [
                'monthly_fee' => 'required|numeric|min:0|max:100000',
                'name' => 'required|max:255',
                'genre_id' => 'required|numeric',
                'description' => 'sometimes|string|max:300',
                'viewing_restriction' => 'required|numeric|in:0,1',
                'set_back_number_sale' => 'required|numeric|in:0,1',
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $planData = $request->only('name', 'monthly_fee', 'genre_id', 'viewing_restriction', 'set_back_number_sale');
            $this->transactionStart();
            $plan = $this->planService->create($planData);

            if (isset($plan['error'])) {
                return $this->responseFail($plan['error']);
            }

            return $this->responseSuccess($plan, trans('plan.message.create_success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }

    public function update(Request $request, $id)
    {
        $plan = $this->planService->getById($id);

        if (empty($plan)) {
            return $this->responseFail(trans('plan.message.plan_not_found'), $plan,
                AppConfig::HTTP_RESPONSE_STATUS_NOT_FOUND);
        }

        if (Gate::forUser(Auth::guard('api')->user())->denies('is-owner', $plan)) {
            return $this->responseFail(trans('plan.message.can_not_update'), '', AppConfig::HTTP_RESPONSE_STATUS_NOT_AUTHORIZED);
        }

        if (!$this->customValidate($request, [
            'monthly_fee' => 'sometimes|numeric',
            'name' => 'sometimes|max:255',
            'genre_id' => 'sometimes|numeric',
            'description' => 'sometimes|string|max:300',
            'viewing_restriction' => 'sometimes|numeric|in:0,1',
            'set_back_number_sale' => 'sometimes|numeric|in:0,1',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $planData = $request->only('name', 'monthly_fee', 'genre_id', 'viewing_restriction', 'set_back_number_sale');
        $this->transactionStart();
        $plan = $this->planService->update($plan, $planData);

        if (isset($plan['error'])) {
            return $this->responseFail($plan['error']);
        }

        return $this->responseSuccess($plan, trans('plan.message.update_success'));
    }
}
