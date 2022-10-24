<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;
use App\Models\Plan;
use App\Models\User;
use App\Services\PaymentHistoryService;
use App\Services\PlanService;
use Illuminate\Http\Request;

class StripeController extends ApiController
{
    private PlanService $planService;
    private PaymentHistoryService $paymentHistoryService;

    /**
     * @param PlanService $planService
     * @param PaymentHistoryService $paymentHistoryService
     */
    public function __construct(
        PlanService $planService,
        PaymentHistoryService $paymentHistoryService
    ) {
        parent::__construct();
        $this->planService = $planService;
        $this->paymentHistoryService = $paymentHistoryService;
    }

    public function registerUser() {
        try {
            $user = auth('api')->user();
            $user->createOrGetStripeCustomer();

            return $this->responseSuccess(trans('success'), trans('success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }

    public function updatePaymentMethod() {
        try {
            $user =  auth('api')->user();

            return $user->createSetupIntent();
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }

    public function addPaymentMethod(Request $request) {
        try {
            $user =  auth('api')->user();
            $paymentMethod = $request->get('payment_method');

            $user->addPaymentMethod($paymentMethod['id']);

            return $this->responseSuccess(trans('success'), trans('success'));
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }

    public function pay(Request $request) {
        try {
            if (!$this->customValidate($request, [
                'plan_id' => 'required'
            ])) {
                return $this->responseFail($this->getValidationErrors());
            }

            $user = auth('api')->user();
            $plan = $this->planService->getById($request->get('plan_id'));

            $paymentMethod = $user->paymentMethods()->first()->toArray()['id'];
            $user->charge($plan->monthly_fee, $paymentMethod);
            $tripePayment = $user->charge($plan->monthly_fee, $paymentMethod)->toArray();

            if ($tripePayment && $tripePayment['status'] == 'succeeded') {
                $this->paymentHistoryService->createSuccessByUser($user, $plan->id, $tripePayment['id']);
                return $this->responseSuccess(trans('success'), trans('success'));
            } else {
                $this->paymentHistoryService->createFalseByUser($user, $plan->id, $tripePayment['id']);
                return $this->responseFail('stripe payment false');
            }
        } catch (CustomException $exception) {
            return $this->responseFail($exception);
        }
    }
}
