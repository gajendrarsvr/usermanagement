<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Utility\CommonUtility;
class UsersApiController extends Controller
{
    public function index()
    {
        try{
            $checkAccess = Gate::check('user_access');
            if($checkAccess == true){
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
            } else{
                $msg = trans('messages.user.not_authorize');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
            
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
            $checkAccess = Gate::check('user_show');
            if($checkAccess == true ){
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
            }else {
                $msg = trans('messages.user.not_authorize');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
            
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
            $checkAccess = Gate::check('user_delete');
            if($checkAccess == true){
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
            } else {
                $msg = trans('messages.user.not_authorize');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
            
        }catch (\Exception $e) {
            DB::rollback();
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }
}
