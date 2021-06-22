<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Auth;

use App\Utility\CommonUtility;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonEmailSender;
//models
use App\Models\User;
use App\Models\PasswordResets;

//request
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyTokenRequest;
use App\Http\Requests\Auth\SetNewPasswordRequest;


class AuthController extends Controller
{

   /**
    * Function for get user logged in
   */
   public function login(LoginRequest $request)  {
       $postData = $request->all();
       if(auth()->attempt(['email' => $postData['email'], 'password' => $postData['password']])){
          $user = User::select('id','first_name','last_name','email','phone')->where('email', $postData['email'])->first();
          $token = $user->createToken('user-management');
          return response()->json([
              'status'=> 200,
              'message' => 'You have been logged in successfully',
              'data' => [
                  'user' => $user,
                  'token' => [
                      'accessToken' => $token->accessToken,
                      'expires_at' => $token->token->expires_at
                  ]
              ]
          ]);

         $msg = trans('messages.auth.login_success'); $code = CommonUtility::SUCCESS_CODE;
         return CommonUtility::renderJson($code, $msg,[
                  'user' => $user,
                  'token' => [
                      'accessToken' => $token->accessToken,
                      'expires_at' => $token->token->expires_at
                  ]
         ]);

       } else {
           $msg = trans('messages.auth.login_failure'); $code = CommonUtility::ERROR_CODE;
           return CommonUtility::renderJson($code, $msg);
      }
   }

    /**
    * Fucntion for get user register
    */
    public function register(RegisterRequest $request) {
         $postData = $request->all();
         $userCreated = User::create([
             'first_name' => $postData['first_name'],
             'last_name' => $postData['last_name'],
             'email' => $postData['email'],
             'password' =>  Hash::make($postData['password'])
         ]);
         if($userCreated) {
              $msg = trans('messages.auth.register_success'); $code = CommonUtility::SUCCESS_CODE;
              return CommonUtility::renderJson($code, $msg);
         } else {
            $msg = trans('messages.auth.register_failure'); $code = CommonUtility::ERROR_CODE;
            return CommonUtility::renderJson($code, $msg);
         }
    }


    /**
     * Fuction for send reset password link
     */
    public function forgotPassword(ForgotPasswordRequest $request) {
        try{
                DB::beginTransaction();
                $postData = $request->all();
                $userModel = User::where('email',$postData['email'])->first();
                if(!$userModel) {
                    $msg = trans('messages.auth.reset_email_invalid'); $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
                //Create Password Reset Token
                $token = Str::random(60);
                $reset_link = \url('/reset-password?token='.$token);
                PasswordResets::create([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]);
                //send email code here
                Mail::to($request->email)->send(new CommonEmailSender([
                    'subject' => 'User Management - Reset your password',
                    'token'   =>  $token,
                    'reset_link' => $reset_link,
                    'emailData' => $userModel
                ]));
                DB::commit();
                $msg = trans('messages.auth.reset_link'); $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg);

         }catch (\Exception $e) {
             DB::rollback();
             CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
             return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    /**
     * Function for verify token
     */
    public function verifyToken(VerifyTokenRequest $request) {
        $postData = $request->all();
        $resetModel = PasswordResets::where([
            'token' => $postData['token']
            ])->where('created_at','>=', (Carbon::now()->subHours(2)))->get()->first();
        if(!$resetModel) {
             $msg = trans('messages.auth.token_unverify'); $code = CommonUtility::ERROR_CODE;
             return CommonUtility::renderJson($code, $msg);
        } else {
             $msg = trans('messages.auth.token_verify'); $code = CommonUtility::SUCCESS_CODE;
             return CommonUtility::renderJson($code, $msg);
        }
    }

    /**
     * Function for set new password
     */
    public function setNewPassword(SetNewPasswordRequest $request) {
        $postData = $request->all();
        //return $postData;
        $resetModel = PasswordResets::where([
            'token' => $postData['token']
            ])->get()->first();
        if(!$resetModel) {
             $msg = trans('messages.auth.invalid_token'); $code = CommonUtility::ERROR_CODE;
             return CommonUtility::renderJson($code, $msg);
        } else {
            $userModel = User::where(['email' => $resetModel->email])->first();
            if(!$userModel) {
                $msg = trans('messages.auth.token_no_user'); $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            } else {
                $userModel->update([
                    'password' =>  Hash::make(trim($postData['password_confirmation']))
                ]);
                PasswordResets::where(['token' => $postData['token']])->delete();
                $msg = trans('messages.auth.setpassword_success'); $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }
    }

    /**
     * Function for logout the app
     */
    public function logout(Request $request) {
        try{
             $request->user()->token()->revoke();
             $msg = trans('messages.auth.logout');
             $code = CommonUtility::SUCCESS_CODE;
            return CommonUtility::renderJson($code, $msg);
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }


}
