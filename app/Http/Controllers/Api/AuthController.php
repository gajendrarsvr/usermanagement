<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\CommonEmailSender;
//models
use App\Models\User;

//request
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;

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

    public function forgotPassword(ForgotPasswordRequest $request) {
        $postData = $request->all();
        $userModel = User::where('email',$postData['email'])->first();
        if(!$userModel) {
            return response()->json(['status'=> 401,'message'=>'Your email does not associated with any account.']);
        }

        //send email code here

        Mail::to('gajendra.pawar@rsvrtech.com')->send(new CommonEmailSender([
            'subject' => 'User Management - Reset your password ',
            'emailData' => $userModel
        ]));




        //return $userModel;
    }




}
