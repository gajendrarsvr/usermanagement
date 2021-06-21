<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Auth;

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
       } else {
          return response()->json(['status'=> 401,'message'=>'User creadentials not match']);
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
             return response()->json(['status'=> 200,
              'message' => 'You have been registered successfully']);
         } else {
            return response()->json(['status'=> 401,'message'=>'Opps! facing issue in create new user']);
         }
    }


    /**
     * Fuction for send reset password link
     */
    public function forgotPassword(ForgotPasswordRequest $request) {
        $postData = $request->all();
        $userModel = User::where('email',$postData['email'])->first();
        if(!$userModel) {
            return response()->json(['status'=> 401,'message'=>'Your email does not associated with any account.']);
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
        // Mail::to('gajendra.pawar@rsvrtech.com')->send(new CommonEmailSender([
        //     'subject' => 'User Management - Reset your password',
        //     'token'   =>  $token,
        //     'reset_link' => $reset_link,
        //     'emailData' => $userModel
        // ]));
        return response()->json([
              'status'=> 200,
              'message' => 'We have sent reset password link on your email address please check inbox'
        ]);
    }

    /**
     * Function for verify token
     */
    public function verifyToken(VerifyTokenRequest $request) {
        $postData = $request->all();
        $resetModel = PasswordResets::where([
            'token' => $postData['token'],
            'created_at' => Carbon::now()->subHours(2)
            ])->get()->first();
        if(!$resetModel) {
            return response()->json([
                'status'=> 401,
                'message' => 'Invalid token/expire token'
            ]);
        } else {
            return response()->json([
                'status'=> 200,
                'message' => 'Token verified successfully'
            ]);
        }
    }

    /**
     * Function for set new password
     */
    public function setNewPassword(SetNewPasswordRequest $request) {
        $postData = $request->all();
        $resetModel = PasswordResets::where([
            'token' => $postData['token']
            ])->get()->first();
        if(!$resetModel) {
            return response()->json([
                'status'=> 401,
                'message' => 'Invalid token/expire token'
            ]);
        } else {

            $userModel = User::where(['email' => $resetModel->email])->first();
            if(!$userModel) {
                return response()->json([
                    'status'=> 200,
                    'message' => 'Token is not associated with any user'
                ]);
            } else {
                $userModel->update([
                    'password' =>  Hash::make($resetModel['password_confirmation'])
                ]);
                DB::table('password_resets')->where('token',$postData['token'])->delete();
                return response()->json([
                    'status'=> 200,
                    'message' => 'User password changed successfully',
                ]);
            }

        }

    }


}
