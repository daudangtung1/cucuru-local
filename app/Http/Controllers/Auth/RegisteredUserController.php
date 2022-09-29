<?php

namespace App\Http\Controllers\Auth;

use App\Cognito\CognitoClient;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Ellaisys\Cognito\Auth\RegistersUsers;
use Ellaisys\Cognito\AwsCognitoClient;
use Ellaisys\Cognito\Exceptions\InvalidUserFieldException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class RegisteredUserController extends ApiController
{
    use RegistersUsers;

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function signup(Request $request, array $clientMetadata = null)
    {
        $cognitoRegistered = false;
        $user = [];

        if (!$this->customValidate($request, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required'],
        ])) {
            return $this->responseFail($this->getValidationErrors());
        }

        $data = $request->all();
        $collection = collect($data);

        //Register User in Cognito
        $cognitoRegistered = $this->createCognitoUser($collection, $clientMetadata, config('cognito.default_user_group', null));

        if (isset($cognitoRegistered['UserConfirmed']) && isset($cognitoRegistered['UserSub'])) {
            User::create([
               'username' => $request->email,
               'email' => $request->email,
               'password' => Hash::make($request->password),
            ]);
        }

        return $this->responseSuccess($cognitoRegistered, __('auth.cognito.signup_success'));
    }

    public function createCognitoUser(Collection $request, array $clientMetadata = null, string $groupname = null)
    {
        $attributes = [];
        $userFields = config('cognito.cognito_user_fields');

        foreach ($userFields as $key => $userField) {
            if ($request->has($userField)) {
                $attributes[$key] = $request->get($userField);
            } else {
                Log::error('RegistersUsers:createCognitoUser:InvalidUserFieldException');
                Log::error("The configured user field {$userField} is not provided in the request.");
                throw new InvalidUserFieldException("The configured user field {$userField} is not provided in the request.");
            }
        }

        $userKey = $request->has('username') ? 'username' : 'email';
        $password = $request->has('password') ? $request['password'] : null;

        return app()->make(CognitoClient::class)->register($request[$userKey], $password, $attributes);
    }
}
