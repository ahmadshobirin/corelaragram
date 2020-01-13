<?php

use Illuminate\Http\Request;

Route::get('/',function(){
    return response()->json([
        'status' => true,
        'description' => "Welcome to API Laragram "
    ]);
});

Route::post('login', 'Api\UsersController@login');
Route::post('register', 'Api\UsersController@register');

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('logout', 'Api\UsersController@logout');
    Route::get('user', 'Api\UsersController@me');

    Route::post('post','Api\PostController@store');
});

// Route::middleware('auth:jwt')->get('/user', function (Request $request) {
//     return $request->user();
// });
