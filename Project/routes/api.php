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
Route::middleware('auth:api')->group(function(){

    Route::get('auth-user', 'AuthUserController@show');

    Route::resource('/posts', 'PostController');
    Route::resource('/posts/{post}/like', 'PostLikeController');
    Route::resource('/users', 'UserController');
    Route::resource('/users/{user}/posts', 'UserPostController');
    Route::resource('/friendRequest', 'FriendRequestController');
    Route::resource('/friendRequestResponse', 'FriendRequestResponseController');
});


