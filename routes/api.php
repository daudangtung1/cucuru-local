<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\V1\PrerequisiteController;
use App\Http\Controllers\Api\V1\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('signup', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'signup']);
Route::post('email/verify', [\App\Http\Controllers\Auth\VerifyEmailController::class, 'verify']);
Route::post('signin', [\App\Http\Controllers\Auth\AuthenticatedController::class, 'signin']);
Route::post('refresh', [\App\Http\Controllers\Auth\AuthenticatedController::class, 'refreshToken']);
Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedController::class, 'logout'])->middleware('aws-cognito');

Route::post('/v1/auth/login', LoginController::class);
Route::get('/v1/prerequisites', [PrerequisiteController::class, 'getPageInfo']);

Route::get('/v1/faqs', [FaqController::class, 'index']);
Route::get('/v1/faqs/{id}', [FaqController::class, 'show']);


Route::group([
    'prefix' => 'v1',
    'namespace' => 'Api\V1',
    'middleware' => ['aws-cognito'],
], function () {
    Route::get('posts', 'PostController@index');
    Route::post('posts', 'PostController@store');
    Route::get('posts/{id}', 'PostController@show');
    Route::post('posts/{id}', 'PostController@update');
    Route::delete('posts/{id}', 'PostController@destroy');

    // Route::post('/faqs', [FaqController::class, 'create']);

    // Group for comment
    Route::group([
        'prefix' => 'comments'
    ], function () {
        Route::get('/post/{postId}', 'CommentController@index');
        Route::post('/', 'CommentController@store');
        Route::delete('/{id}', 'CommentController@delete');
    });

    Route::get('payments', 'PaymentController@index');

    Route::post('user/profile', 'ProfileController@update');
    Route::get('user/follows', 'UserController@getFollow');
    Route::get('user/followers', 'UserController@getFollower');
    Route::get('affiliates/me/code', 'UserController@getInviteCode');

    Route::get('me', 'UserController@me');
    Route::post('plans', 'PlanController@store');
    Route::put('plans/{id}', 'PlanController@update');

    Route::get('notifications', 'NotificationController@index');
    Route::post('notification-setting/change', 'NotificationSettingController@update');

    Route::get('/categories', 'CategoryController@index');
    Route::post('affiliate/register', 'AffiliateController@register');

    Route::group([
        'namespace' => 'stripe',
    ], function (){
        Route::get('register-user', [StripeController::class, 'registerUser']);
        Route::get('update-payment-method', [StripeController::class, 'updatePaymentMethod']);
        Route::post('add-payment-method', [StripeController::class, 'addPaymentMethod']);
        Route::get('pay', [StripeController::class, 'pay']);
    });
});
