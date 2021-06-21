<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Apps\AppsCreateRequest;
use App\Models\Apps;
use App\Models\AppsUser;
use App\Utility\CommonUtility;


class AppsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
             $getAll = Apps::all();
             $msg = trans('messages.apps.apps_list');
             $code = CommonUtility::SUCCESS_CODE;
            return CommonUtility::renderJson($code, $msg,$getAll);
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppsCreateRequest $request)
    {
         try{
            $postData = $request->all();
            $created = Apps::create([
                'app_id' => isset($postData['app_id'])?$postData['app_id']:null,
                'app_name' => isset($postData['app_name'])?$postData['app_name']:null,
                'app_desc' => isset($postData['app_desc'])?$postData['app_desc']:null,
                'app_link' => isset($postData['app_link'])?$postData['app_link']:null
            ]);
            if(!$created) {
                $msg = trans('messages.apps.app_not_create');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            } else {
                $msg = trans('messages.apps.create');
                $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
             $model = Apps::find($id);
             if(!$model) {
                $msg = trans('messages.apps.app_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
             }
             $msg = trans('messages.apps.app_edit');
             $code = CommonUtility::SUCCESS_CODE;
            return CommonUtility::renderJson($code, $msg,$model);
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $postData = $request->all();

            $model = Apps::find($id);
            if(!$model) {
                $msg = trans('messages.apps.app_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }

            $updated = $model->update([
                'app_id' => isset($postData['app_id'])?$postData['app_id']:null,
                'app_name' => isset($postData['app_name'])?$postData['app_name']:null,
                'app_desc' => isset($postData['app_desc'])?$postData['app_desc']:null,
                'app_link' => isset($postData['app_link'])?$postData['app_link']:null
            ]);
            if(!$updated) {
                $msg = trans('messages.apps.app_not_create');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            } else {
                $msg = trans('messages.apps.app_update');
                $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $model = Apps::find($id);
            if(!$model) {
                $msg = trans('messages.apps.app_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }

            $deleted = $model->delete();
            if(!$deleted) {
                $msg = trans('messages.apps.app_not_create');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            } else {
                $msg = trans('messages.apps.app_delete');
                $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }
    }
}
