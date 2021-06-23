<?php

return [
    'auth' => [
        'login_success' => 'You have logged in successfully',
        'login_failure' =>'Login credential is invalid',
        'register_success' =>"You have been registered successfully",
        'register_failure' =>"Opps! facing issue with create your account",
        'reset_link' => 'We have successfully sent you a reset password link please check your email inbox',
        'reset_email_invalid' => 'Your email address is invalid',
        'reset_link_failure' => 'Opps! currently we are facing issue with sending you a reset password link',
        'token_verify' => 'Token verified successfully',
        'token_unverify' => 'Your link has been invalid/expired',
        'setpassword_success' => 'Your link has been invalid/expired',
        'invalid_token' => 'You have provided invalid/expired token',
        'token_no_user' => 'Token is not associated with any user',
        'logout' => 'You have been logged out successfully'
    ],
    'user' => [
        'users' => 'Users Data',
        'empty_users' =>'There is not record found',
        'not_authorize' =>"Don't have a permission",
        'user_create' =>'User created successfully',
        'user_not_create' =>'User not created',
        'user_update' =>'User update successfully',
        'user_not_found' =>'User not found',
        'user_delete' =>'User delete successfully',
        'user_record'=>'User record',
        'user_subscription_not_found' =>'User subscription not found',
        'user_app_not_found' =>'User apps not found',
        'user_exist' =>'User already exist with this email id',
        'user_email_not_found' =>'Email field is empty please first add email after that you logged in  '

    ],
    'subscription' =>[
        'subscriptions'=>'Subscriptions Data',
        'empty_subscription' =>'There is not record found',
        'subscription_create' =>'Subscription created successfully',
        'subscription_not_create' =>'Subscription not created',
        'subscription_update' =>'Subscription update successfully',
        'subscription_not_found' =>'Subscription not found',
        'subscription_delete' =>'Subscription delete successfully',
        'subscription_record'=>'Subscription record'
    ],
        'apps' => [
        'apps_list' => 'All apps list retrieved',
        'empty' =>'No app found',
        'not_authorize' =>"Don't have a permission",
        'create' =>'New app created successfully',
        'app_not_create' =>'Facing issue in creating new app',
        'app_update' =>'App update successfully',
        'app_not_found' =>'Invalid app id assigned',
        'app_delete' =>'App deleted successfully',
        'app_edit'=>'App record retrieved'
    ],
    'apps_user' => [
        'users_list' => 'All users apps list retrieved',
        'users_add' =>'User assign new apps',
        'users_add_error' =>"Facing issue to assign the app to user",
        'users_not_exist' =>"App user not exist"
    ],

    'user_setting' => [
        'user_setting_list' => 'All user setting list retrieved',
        'empty' =>'No user setting found',
        'not_authorize' =>"Don't have a permission",
        'create' =>'New user setting created successfully',
        'user_setting_not_create' =>'Facing issue in creating new user setting',
        'user_setting_update' =>'User Setting update successfully',
        'user_setting_not_found' =>'User Setting not found',
        'user_setting_delete' =>'User Setting deleted successfully',
        'user_setting_edit'=>'User Setting record retrieved'
    ],
];
