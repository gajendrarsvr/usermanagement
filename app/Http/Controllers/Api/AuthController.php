<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\AuthRequest;
use App\Models\User;
use Auth;
class AuthController extends Controller
{
   public function login(AuthRequest $request)
   {
       $input = $request->all();
       $email = $input['email'];
       $password = $input['password'];

      if(auth()->attempt(['email'=>$email, 'password'=>$password])){
          $user = User::where('email', $email)->first();
        $token = $user->createToken('user-management');
        return ['token' => $token];
      }else {
        return response()->json(['status'=> 401,'message'=>'user creadential not match']);
      }
   }
}
