<?php

use Illuminate\Http\Request;

Route::post('login', 'Api\UsersController@login');
Route::post('register', 'Api\UsersController@register');

Route::get('user', 'Api\UsersController@me')->middleware('jwt.verify');

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::get('logout', 'Api\UsersController@logout');
});

// Route::middleware('auth:jwt')->get('/user', function (Request $request) {
//     return $request->user();
// });
