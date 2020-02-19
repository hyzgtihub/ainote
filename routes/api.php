<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
//         return 123;
});

// Route::any('test','api\PhotoController@index');

Route::apiResources([
    'photos' => 'api\PhotoController',
//    'user/wxMiniAuth' => 'api\UserAuthorize@wxMiniAuth',
]);

Route::any('user/wxMiniLogin','api\UserAuthorize@wxMiniLogin');
Route::any('user/infoInput','api\UserAuthorize@infoInput');
Route::any('user/bind','api\UserAuthorize@bind');

Route::any('index','api\IndexController@index');

