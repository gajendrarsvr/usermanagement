<?php
Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api'], function () {
    Route::post('login','AuthController@login');
    Route::post('register','AuthController@register');
    Route::post('forgot-password','AuthController@forgotPassword');
    Route::post('reset-password','AuthController@resetPassword');
    Route::post('logout','AuthController@logout');
});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');
});
