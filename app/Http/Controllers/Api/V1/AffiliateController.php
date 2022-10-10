<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CustomException;
use App\Http\Controllers\ApiController;
use App\Jobs\SendEmailJob;
use App\Mail\AffiliateMail;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AffiliateController extends ApiController
{
    /**
     * @var AffiliateService
     */
    protected $affiliateService;

    /**
     * PlanController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->affiliateService = new AffiliateService();
    }

    public function register(Request $request)
    {
        if (!$this->customValidate($request, [
            'email' => 'required|unique:affiliates|email',
            'confirm_email' => 'required|same:email',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $affiliate = $this->affiliateService->register($request->only('email'));

        if (isset($affiliate['error'])) {
            return $this->responseFail($affiliate['error']);
        }
        dispatch(new SendEmailJob($request->only('email')));

        return $this->responseSuccess($affiliate, __('affiliate.register_success'));
    }
}
