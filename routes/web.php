<?php

Route::get('/subscribe', 'TenantController@showRegistrationForm')->name('subscribe');
Route::post('/subscribe', 'TenantController@register');

Route::middleware('tenant')->namespace('Bookkeeping')->domain('{tenant}.localhost')->group(function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', 'UserController@index');
    });

    Auth::routes();
});

Route::get('/', function () {
    /*
     * @TODO implement a view
     */
    return '@TODO implement a view';
});
