<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController
{
    /**
     * @response {
     *  "data": {
     *    "id": 4,
     *    "username": "johndoe",
     *    "email": "johndoe@example.com"
     *  }, 
     *  "tokens": {
     *    "access_token": "2|qhbR1p8oGHwzjbpJmmYr0C2pDlFXiUHtt6x91qsb"
     *  }
     * }
     */
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return new LoginResource($user);
    }
}
