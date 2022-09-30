<?php

namespace App\Http\Middleware;

use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Closure;
use Ellaisys\Cognito\Exceptions\InvalidTokenException;
use Ellaisys\Cognito\Exceptions\NoTokenException;
use Ellaisys\Cognito\Http\Middleware\BaseMiddleware;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AwsCognitoAuthenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guards = null)
    {
        $guard = '';
        $middleware = '';
        $countRouteMiddleware = 0;

        try {
            $routeMiddleware = $request->route()->middleware();
            if (empty($routeMiddleware) || (($countRouteMiddleware = count($routeMiddleware)) < 1)) {
                return response()->json([
                    '_status' => Response::HTTP_UNAUTHORIZED,
                    '_success' => false,
                    '_messages' => __('auth.cognito.unauthorized_request'),
                    '_data' => null
                ], Response::HTTP_UNAUTHORIZED);
            } else {
                ($countRouteMiddleware > 0) ? ($guard = $routeMiddleware[0]) : null;
                ($countRouteMiddleware > 1) ? ($middleware = $routeMiddleware[1]) : null;
            }

            //Authenticate the request
            $this->authenticate($request, $guard);
            Auth::guard('api')->getUserByAccessToken(request()->bearerToken());
            return $next($request);
        } catch (Exception $e) {
            if ($e instanceof NoTokenException) {
                return response()->json([
                    '_status' => Response::HTTP_UNAUTHORIZED,
                    '_success' => false,
                    '_messages' => __('auth.cognito.no_token_exception'),
                    '_data' => null
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof InvalidTokenException) {
                return response()->json([
                    '_status' => Response::HTTP_UNAUTHORIZED,
                    '_success' => false,
                    '_messages' => __('auth.cognito.invalid_token_exception'),
                    '_data' => null
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof CognitoIdentityProviderException) {
                return response()->json([
                    '_status' => Response::HTTP_UNAUTHORIZED,
                    '_success' => false,
                    '_messages' => __('auth.cognito.token_has_revoked'),
                    '_data' => null
                ], Response::HTTP_UNAUTHORIZED);
            }

            //Raise error in case of generic error
            if ($guard == 'web') {
                return redirect('/');
            } else {
                return response()->json([
                    '_status' => Response::HTTP_UNAUTHORIZED,
                    '_success' => false,
                    '_messages' => $e->getMessage(),
                    '_data' => null
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
    }
}
