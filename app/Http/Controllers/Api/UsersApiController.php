<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use App\Models\AppsUser;
use App\Models\Subscription;
use App\Http\Resources\SubscriptionResource;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Utility\CommonUtility;
use App\Utility\MailUtility;
class UsersApiController extends Controller
{
    public function index()
    {
        try{
            // $checkAccess = Gate::check('user_access');
            // if($checkAccess == true){
                $userData = new UserResource(User::with(['roles'])->get());
                if($userData != null){
                    $msg = trans('messages.user.users');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = $userData;
                    return CommonUtility::renderJson($code, $msg,$data);
                }else {
                    $msg = trans('messages.user.empty_users');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
            // } else{
            //     $msg = trans('messages.user.not_authorize');
            //     $code = CommonUtility::ERROR_CODE;
            //     return CommonUtility::renderJson($code, $msg);
            // }
            
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }   
    }

    public function store(StoreUserRequest $request)
    {
        try{
            $user = User::create($request->all());

            $usersData = new UserResource($user);
            if($usersData!= null){
                $msg = trans('messages.user.user_create');
                $code = CommonUtility::SUCCESS_CODE;
                $data = $usersData;
                return CommonUtility::renderJson($code, $msg,$data);
            }else {
                $msg = trans('messages.user.user_not_create');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }            
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function show($id)
    {
        try{
            // $checkAccess = Gate::check('user_show');
            // if($checkAccess == true ){
                $user = User::find($id);
                if($user != null){
                    $msg = trans('messages.user.user_record');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = new UserResource($user->load(['roles']));
                    return CommonUtility::renderJson($code, $msg,$data);
                }else {
                    $msg = trans('messages.user.user_not_found');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
            // }else {
            //     $msg = trans('messages.user.not_authorize');
            //     $code = CommonUtility::ERROR_CODE;
            //     return CommonUtility::renderJson($code, $msg);
            // }
            
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try{
            $user = User::find($id);
            if($user!= null){
                $user->update($request->all());
                $user->roles()->sync($request->input('roles', []));

                $usersData = new UserResource($user);
                $msg = trans('messages.user.user_update');
                $code = CommonUtility::SUCCESS_CODE;
                $data = $usersData;
                return CommonUtility::renderJson($code, $msg,$data);
            }else {
                $msg = trans('messages.user.user_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
            
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function destroy($id)
    {

        try{
            // $checkAccess = Gate::check('user_delete');
            // if($checkAccess == true){
                $user = User::find($id);
                if($user != null){
                    $user->delete();
                    $msg = trans('messages.user.user_delete');
                    $code = CommonUtility::SUCCESS_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }else {
                    $msg = trans('messages.user.user_not_found');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
            // } else {
            //     $msg = trans('messages.user.not_authorize');
            //     $code = CommonUtility::ERROR_CODE;
            //     return CommonUtility::renderJson($code, $msg);
            // }
            
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function getAllSubscriptionDetail($id)
    {
        try{
            $user = User::find($id);
            if($user != null){
                $subscription = Subscription::where('user_id',$id)->with(['subscriptionPlans'])->get();
                if(count($subscription) != 0){
                    $subscriptionData = new SubscriptionResource($subscription);
                    $msg = trans('messages.subscription.subscription_record');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = $subscriptionData;
                    return CommonUtility::renderJson($code, $msg,$data);
                } else {
                    $msg = trans('messages.subscription.empty_subscription');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
                
            }else {
                $msg = trans('messages.user.user_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function getSubscriptionDetail($id, $subscriptionId)
    {
        try{
            $user = User::find($id);
            if($user != null){
                $subscription = Subscription::where('user_id',$id)->where('id',$subscriptionId )->with(['subscriptionPlans'])->first();
                if($subscription != null){
                    $subscriptionData = new SubscriptionResource($subscription);
                    $msg = trans('messages.subscription.subscriptions');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = $subscriptionData;
                    return CommonUtility::renderJson($code, $msg,$data);
                }else {
                    $msg = trans('messages.user.user_subscription_not_found');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
                
            }else {
                $msg = trans('messages.user.user_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function getAllAppsDetails($id)
    {
        try{
            $user = User::find($id);
            if($user != null){
                $appsUser = AppsUser::where('user_id',$id)->with(['app_detail'])->get();
                if(count($appsUser) != 0){
                    // $subscriptionData = new SubscriptionResource($subscription);
                    $msg = trans('messages.apps.apps_list');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = $appsUser;
                    return CommonUtility::renderJson($code, $msg,$data);
                } else {
                    $msg = trans('messages.subscription.empty');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
                
            }else {
                $msg = trans('messages.user.user_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function getAppsDetail($id, $appId)
    {
        try{
            $user = User::find($id);
            if($user != null){
                $appDetail = AppsUser::where('user_id',$id)->where('id',$appId )->with(['app_detail'])->first();
                if($appDetail != null){
                   
                    $msg = trans('messages.apps.app_edit');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = $appDetail;
                    return CommonUtility::renderJson($code, $msg,$data);
                }else {
                    $msg = trans('messages.user.user_app_not_found');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
                
            }else {
                $msg = trans('messages.user.user_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function mail(Request $request){
        $mailbox = array();
        $userData = User::where('email','=',$request->email)->first();

        if(!$userData){
            $errorMessage = "No client account was found with the email address you entered.";
            return redirect('/forget-password')->with('errorMessage', $errorMessage);
        }
        else{
            $email = $request->email;
            $name = $userData->name;
            $token = base64_encode($email.time().$name);
            $userData->remember_token = $token;
            $userData->save();
            $link = env('APP_URL', '');
            $description = 'Dear '.$userData->first_name.' ('.$userData->email.')!<br/><br/>Recently a request was submitted to reset your password. If you did not request this, please ignore this email. It will expire and become useless in 2 hours time.
 <br/><br/>To reset your password, please <a href="'.$link.'/reset-password?token='.$token.'" target="new"><b>Click Here</b> </a>! <br/><br/>
 When you visit the link above, you will have the opportunity to choose a new password.';

            $mailArray = array(
                "header" => "Reset Password",
                "description" => $description,
                "footer" => "System Generated Email"
            );

            $mailbox['mail_body'] = json_encode($mailArray);
            $mailbox['category'] = "Reset password";
            $mailbox['mail_to'] = $userData->email;
            $mailbox['subject'] = "Your login details for Updesco Technical";
            $mailbox['layout'] = "forgot-password";
            $mailbox['save'] = [];
            return MailUtility::emailTo($mailbox);
        }
    }
}
