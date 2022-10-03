<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Ellaisys\Cognito\Auth\AuthenticatesUsers as CognitoAuthenticatesUsers;
use Ellaisys\Cognito\AwsCognitoClaim;
use Ellaisys\Cognito\Exceptions\NoLocalUserException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthenticatedController extends ApiController
{
    use CognitoAuthenticatesUsers;

    public function signin(Request $request)
    {
        if (!$this->customValidate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $collection = collect($request->all());

        if ($claim = $this->attemptLogin($collection, 'api', 'email', 'password', true)) {
            if ($claim instanceof AwsCognitoClaim) {
                return $this->responseSuccess($claim->getData(), __('auth.cognito.login_success'));
            } else {
                return $this->responseFail($claim);
            }
        }
    }

    protected function attemptLogin(Collection $request, string $guard = 'web', string $paramUsername = 'email', string $paramPassword = 'password', bool $isJsonResponse = false)
    {
        try {
            //Get key fields
            $keyUsername = 'email';
            $keyPassword = 'password';
            $rememberMe = $request->has('remember') ? $request['remember'] : false;

            $credentials = [
                $keyUsername => $request[$paramUsername],
                $keyPassword => $request[$paramPassword]
            ];

            //Authenticate User
            $claim = Auth::guard($guard)->attempt($credentials, $rememberMe);

        } catch (NoLocalUserException $e) {
            if (config('cognito.add_missing_local_user_sso')) {
                $response = $this->createLocalUser($credentials, $keyPassword);

                if ($response) {
                    return __('auth.cognito.login_sso_fail');
                }
            }

            return __('auth.failed');
        } catch (CognitoIdentityProviderException $e) {
            return $this->sendFailedCognitoResponse($e, $isJsonResponse, $paramUsername);
        } catch (Exception $e) {
            return $this->sendFailedLoginResponse($request, $e, $isJsonResponse, $paramUsername);
        }

        if (!($claim instanceof AwsCognitoClaim)) {
            $claim = !empty($claim->original['aws_error_code']) ?
                __('auth.cognito.' . Str::snake($claim->original['aws_error_code'])) : __('auth.cognito.login_fail');
        }

        return $claim;
    }

    private function sendFailedLoginResponse(Collection $request, Exception $exception = null, bool $isJsonResponse = false, string $paramUsername = 'email')
    {
        $message = 'FailedLoginResponse';
        if (!empty($exception)) {
            $message = $exception->getMessage();
        }

        if ($isJsonResponse) {
            return $this->responseFail(__('auth.cognito.login_fail'));
        } else {
            return redirect()->back()->withErrors([$paramUsername => $message]);
        }
    }

    public function refreshToken(Request $request) {
        if (!$this->customValidate($request, [
            'email' => 'required|email',
            'refresh_token' => 'required',
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        try {
            $claim = Auth::guard('api')->refreshToken($request->email, $request->refresh_token);

            if (!isset($claim['AuthenticationResult'])) {
                return $this->responseFail('Refresh token fail');
            }
        } catch (Exception $e) {
            return $this->sendFailedLoginResponse($request->collect(), $e, true);
        }

        return $this->responseSuccess($claim['AuthenticationResult']);
    }

    public function logout() {
        try {
            $claim = Auth::guard('api')->globalSignOut(request()->bearerToken());

            if (!isset($claim['@metadata']) || !isset($claim['@metadata']['statusCode']) || $claim['@metadata']['statusCode'] != 200) {
                return $this->responseFail(__('auth.cognito.logout_fail'));
            }
        } catch (Exception $e) {
            return $this->responseFail(__('auth.cognito.logout_fail'));
        }

        return $this->responseSuccess(__('auth.cognito.logout_success'));
    }
}
