<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'cognito' => [
        'login_sso_fail' => 'Login by SSO fail.',
        'login_fail' => 'Login fail.',
        'login_success' => 'Login success.',
        'unauthorized_request' => 'Unauthorized request.',
        'no_token_exception' => 'No token exception.',
        'invalid_token_exception' => 'Invalid token exception.',
        'signup_success' => 'Register success.',
        'user_not_confirmed_exception' => 'User not confirmed exception.',
        'logout_fail' => 'Logout fail.',
        'logout_success' => 'Logout success.',
        'token_has_revoked' => 'Access Token has been revoked',
        'not_authorized_exception' => 'These credentials do not match in Cognito.',
    ],
    'token_expired' => 'Your session has timed out, please login again', // token_expired
    'token_invalid' => 'You have been logged out, please login again', // Token has invalid
    'token_blacklisted' => 'Token has blacklisted, please login again',
    'token_not_provided' => 'Token not provided',
];
