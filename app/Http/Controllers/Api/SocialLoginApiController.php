<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utility\CommonUtility;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
class SocialLoginApiController extends Controller
{
    public function redirectToGoogle()
    {
        Socialite::driver('google')->stateless()->redirect(); 
    }

    public function handleGoogleCallback()
    {
        try {
            $user =    Socialite::driver('google')->stateless()->user();
            $userId = $user->id;
            $finduser = User::where('google_id', $userId)->first();
            if ($finduser !== null) {
                $token = $finduser->createToken('user-management');
               
                $msg = trans('messages.auth.login_success'); $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg,[
                    'user' => $finduser,
                    'token' => [
                      'accessToken' => $token->accessToken,
                      'expires_at' => $token->token->expires_at
                    ]
                ]);
            } else {
                $findUserLocal = User::where('email', $user->email)->first();
                if($findUserLocal != null) {
                    $msg = trans('messages.user.user_exist');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
                $newUser = new User;
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->google_id = $userId;
                $newUser->save();
                $token = $newUser->createToken('user-management');


                $msg = trans('messages.auth.login_success'); $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg,[
                    'user' => $newUser,
                    'token' => [
                      'accessToken' => $token->accessToken,
                      'expires_at' => $token->token->expires_at
                    ]
                ]);
            }

        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
       }
    }

    public function redirectToFacebook(Request $request)
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function handleFacebookCallback(Request $request)
    {

        try {
            $user = Socialite::driver('facebook')->user();
            if ($user->email === null) {
                    $msg = trans('messages.user.user_email_not_found');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
            }
            $userId = $user->id;
            $finduser = User::where('facebook_id', $user->id)->first();
            if ($finduser !== null) {
                $token = $finduser->createToken('user-management');
               
                $msg = trans('messages.auth.login_success'); $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg,[
                    'user' => $finduser,
                    'token' => [
                      'accessToken' => $token->accessToken,
                      'expires_at' => $token->token->expires_at
                    ]
                ]);
            } else {
                $findUserLocal = User::where('email', $user->email)->first();
                if($findUserLocal != null) {
                    $msg = trans('messages.user.user_exist');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
                $newUser = new User;
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->facebook_id = $userId;
                $newUser->save();
                $token = $newUser->createToken('user-management');
            }
            $msg = trans('messages.auth.login_success'); $code = CommonUtility::SUCCESS_CODE;
            return CommonUtility::renderJson($code, $msg,[
                'user' => $newUser,
                'token' => [
                  'accessToken' => $token->accessToken,
                  'expires_at' => $token->token->expires_at
                ]
            ]);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
       }
    }

    public function redirectToApple()
    {
        return Socialite::driver("sign-in-with-apple")->redirect();
    }

    public function handleAppleCallback(Request $request)
    {
        // get abstract user object, not persisted
        $user = Socialite::driver("sign-in-with-apple")
            ->user();
        
        // or use Socialiter to automatically manage user resolution and persistence
        $user = Socialiter::driver("sign-in-with-apple")
            ->login();
    }
}
