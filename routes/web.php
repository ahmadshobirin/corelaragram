<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('view-image/{endfolder}/{file}', 'Api\PostController@displayImage')
        ->name('image.displayImage');
