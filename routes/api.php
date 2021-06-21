<?php
Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api'], function () {
    Route::post('login','AuthController@login');
    Route::post('register','AuthController@register');
    Route::post('forgot-password','AuthController@forgotPassword');
    Route::post('verify-token','AuthController@verifyToken');
    Route::post('set-new-password','AuthController@setNewPassword');
    Route::post('logout','AuthController@logout');
});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    //Apps
    Route::post('apps/user-assignment', 'AppsApiController@assignApp2User');
    Route::get('apps/user/{id}', 'AppsApiController@getsUsersApp');
    Route::apiResource('apps', 'AppsApiController');


    // Users
    Route::apiResource('users', 'UsersApiController');
    Route::get('user/{id}/subscription', 'UsersApiController@getAllSubscriptionDetail');
    Route::get('user/{id}/subscription/{subscription_id}', 'UsersApiController@getSubscriptionDetail');
    Route::get('user/{id}/apps', 'UsersApiController@getAppsDetails');
    Route::get('user/{id}/apps/{apps_id}', 'UsersApiController@getAppsDetail');

    //Subscriptions
    Route::apiResource('subscriptions', 'SubscriptionApiController');

});
