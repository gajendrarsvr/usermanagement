<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserSettingRequest;
use App\Http\Requests\UpdateUserSettingRequest;
use App\Http\Resources\UserSettingResource;
use App\Models\UserSetting;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Utility\CommonUtility;

class UserSettingApiController extends Controller
{
    public function index()
    {
        try{

        $getAll = new UserSettingResource(UserSetting::all());

        $msg = trans('messages.user_setting.user_setting_list');

        $code = CommonUtility::SUCCESS_CODE;

       return CommonUtility::renderJson($code, $msg,$getAll);

   }catch (\Exception $e) {

       CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());

       return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
   }
    }

    public function store(StoreUserSettingRequest $request)
    {
        try{
        $user_setting = UserSetting::create($request->all());

        $created = (new UserSettingResource($user_setting))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);

            if(!$created) {
                $msg = trans('messages.user_setting.user_setting_not_create');

                $code = CommonUtility::ERROR_CODE;

                return CommonUtility::renderJson($code, $msg);
            } else {

                $msg = trans('messages.user_setting.create');

                $code = CommonUtility::SUCCESS_CODE;

                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {

            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());

            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }

    }

    public function show($user_setting)
    {
        try{
            $user_setting = UserSetting::find($user_setting);

            if(!$user_setting) {

                $msg = trans('messages.user_setting.user_setting_not_found');

                $code = CommonUtility::ERROR_CODE;

                return CommonUtility::renderJson($code, $msg);
            }

            $msg = trans('messages.user_setting.user_setting_edit');

            $code = CommonUtility::SUCCESS_CODE;

            $model = new UserSettingResource($user_setting);

            return CommonUtility::renderJson($code, $msg,$model);

        }catch (\Exception $e) {

        CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());

        return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
    }
    }

    public function update(UpdateUserSettingRequest $request, $user_setting)
    {

        try{

            $user_setting = UserSetting::find($user_setting);

            if(!$user_setting) {

                $msg = trans('messages.user_setting.user_setting_not_create');

                $code = CommonUtility::ERROR_CODE;

                return CommonUtility::renderJson($code, $msg);
            } else {

                $updated = $user_setting->update($request->all());

                $msg = trans('messages.user_setting.user_setting_update');

                $code = CommonUtility::SUCCESS_CODE;

                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {

            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());

            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    public function destroy($user_setting)
    {
        try{

            $user_setting = UserSetting::find($user_setting);

            if(!$user_setting) {

                $msg = trans('messages.user_setting.user_setting_not_found');

                $code = CommonUtility::ERROR_CODE;

                return CommonUtility::renderJson($code, $msg);
            }

            $deleted = $user_setting->delete();

            if(!$deleted) {

                $msg = trans('messages.user_setting.user_setting_not_create');

                $code = CommonUtility::ERROR_CODE;

                return CommonUtility::renderJson($code, $msg);

            } else {

                $msg = trans('messages.user_setting.user_setting_delete');

                $code = CommonUtility::SUCCESS_CODE;

                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {

            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());

            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }
}
