<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\V1\PrerequisiteController;
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
Route::get('prerequisites', 'PrerequisiteController@getPageInfo');

Route::get('/v1/prerequisites', [PrerequisiteController::class, 'getPageInfo']);

Route::group([
    'prefix' => 'v1',
    'namespace' => 'Api\V1',
    'middleware' => ['aws-cognito'],
], function () {
    Route::get('posts', 'PostController@index');
    Route::post('posts', 'PostController@store');
    Route::get('posts/{id}', 'PostController@show');
    Route::put('posts/{id}', 'PostController@update');
    Route::delete('posts/{id}', 'PostController@destroy');

    Route::get('/faqs', [FaqController::class, 'list']);
    Route::get('/faqs/{id}', [FaqController::class, 'show']);
    Route::post('/faqs', [FaqController::class, 'create']);

    Route::post('comment', 'CommentController@store');
    Route::get('payments', 'PaymentController@index');
    Route::post('profile', 'ProfileController@update');
});
