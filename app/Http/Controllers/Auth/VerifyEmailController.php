<?php

namespace App\Http\Controllers\Auth;

use App\Cognito\CognitoClient;
use App\Http\Controllers\ApiController;
use Ellaisys\Cognito\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerifyEmailController extends ApiController
{
    use VerifiesEmails;

    public function verify(Request $request)
    {
        if (!$this->customValidate($request, [
            'email' => 'required|email',
            'confirmation_code' => 'required|numeric',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $response = app()->make(CognitoClient::class)->confirmUserSignUp($request->email, $request->confirmation_code);

        if ($response == 'validation.invalid_user') {
            return $this->responseFail(__('validation.cognito.invalid_user'));
        }

        if ($response == 'validation.invalid_token') {
            return $this->responseFail(__('validation.cognito.invalid_token'));
        }

        if ($response == 'validation.exceeded') {
            return $this->responseFail(__('validation.cognito.exceeded'));
        }

        if ($response == 'validation.confirmed') {
            return $this->responseSuccess(__('validation.cognito.confirmed'));
        }

        return $this->responseSuccess(__('validation.cognito.confirmed'));
    }
}
