<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\FaqController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/auth/login', LoginController::class);

Route::group([
    'prefix' => 'v1',
    'namespace' => 'Api\V1',
], function () {
    Route::get('posts', 'PostController@index');
    Route::post('posts', 'PostController@store');
    Route::get('posts/{id}', 'PostController@show');
    Route::put('posts/{id}', 'PostController@update');
    Route::delete('posts/{id}', 'PostController@destroy');

    Route::get('prerequisites', 'PrerequisiteController@getPageInfo');

    Route::get('/faqs', [FaqController::class, 'list']);

    Route::get('/faqs/{id}', [FaqController::class, 'show']);

    Route::post('/faqs', [FaqController::class, 'create']);
});
