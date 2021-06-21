<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;

use App\Utility\CommonUtility;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;

class SubscriptionApiController extends Controller
{
    public function index()
    {
        try{
            $authUser = auth()->user()->id;
            $subscriptionData = new SubscriptionResource(Subscription::with(['subscriptionPlans'])->get());
            if($subscriptionData != null){
                $msg = trans('messages.subscription.subscriptions');
                $code = CommonUtility::SUCCESS_CODE;
                $data = $subscriptionData;
                return CommonUtility::renderJson($code, $msg,$data);
            }else {
                $msg = trans('messages.subscription.empty_subscriptions');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }

        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        } 
    }
    public function store(StoreSubscriptionRequest $request)
    {
        try{
            $authUser = auth()->user()->id;
            $input = $request->all();
            $subscription  = new Subscription ;
            $subscription->subscription_plan = $input['subscription_plan'];
            $subscription->app_id = $input['app_id'];
            $subscription->user_id = $authUser;

            $subscriptionPlanData = SubscriptionPlan::find($input['subscription_plan']);
            if($subscriptionPlanData != null) {
                $subscriptionPlan = (int)$subscriptionPlanData->plan_type;
                $endDate = date('Y-m-d', strtotime('+'.$subscriptionPlan.' year'));
                $subscription->end_date = $endDate;
                $subscription->start_date = date('Y-m-d');
                $subscription->save();

                $msg = trans('messages.subscription.subscription_create');
                $code = CommonUtility::SUCCESS_CODE;
                $data = new SubscriptionResource($subscription);
                return CommonUtility::renderJson($code, $msg,$data);
            }else {
                $msg = trans('messages.subscription.subscription_id_invalid');
                $code = CommonUtility::SUCCESS_CODE;
                $data = new SubscriptionResource($subscription);
                return CommonUtility::renderJson($code, $msg,$data);
            }
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        } 
        
        // $subscription->save();
    }

    public function update(UpdateSubscriptionRequest $request, $id)
    {
        try{
            $subscription  = Subscription::find($id) ;
            if($subscription != null){
                $authUser = auth()->user()->id;
                $input = $request->all();
                
                $subscription->subscription_plan = $input['subscription_plan'];
                $subscription->app_id = $input['app_id'];
                $subscription->user_id = $authUser;
    
                $subscriptionPlanData = SubscriptionPlan::find($input['subscription_plan']);
                if($subscriptionPlanData != null) {
                    $subscriptionPlan = (int)$subscriptionPlanData->plan_type;
                    $endDate = date('Y-m-d', strtotime('+'.$subscriptionPlan.' year'));
                    $subscription->end_date = $endDate;
                    $subscription->start_date = date('Y-m-d');
                    $subscription->save();
    
                    $msg = trans('messages.subscription.subscription_update');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = new SubscriptionResource($subscription);
                    return CommonUtility::renderJson($code, $msg,$data);
                }else {
                    $msg = trans('messages.subscription.subscription_id_invalid');
                    $code = CommonUtility::ERROR_CODE;
                    return CommonUtility::renderJson($code, $msg);
                }
            }else {
                $msg = trans('messages.subscription.subscriptions_not_found');
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
                $subscription = Subscription::find($id);
                if($subscription != null){
                    $msg = trans('messages.subscription.subscription_record');
                    $code = CommonUtility::SUCCESS_CODE;
                    $data = new SubscriptionResource($subscription->load(['subscriptionPlans']));
                    return CommonUtility::renderJson($code, $msg,$data);
                }else {
                    $msg = trans('messages.subscription.subscription_not_found');
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
            $subscription = Subscription::find($id);
            if($subscription != null){
                $subscription->delete();
                $msg = trans('messages.subscription.subscription_delete');
                $code = CommonUtility::SUCCESS_CODE;
                return CommonUtility::renderJson($code, $msg);
            }else {
                $msg = trans('messages.subscription.subscription_not_found');
                $code = CommonUtility::ERROR_CODE;
                return CommonUtility::renderJson($code, $msg);
            }           
        }catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e->getFile(), $e->getLine(), $e->getMessage());
            return CommonUtility::renderJson(CommonUtility::ERROR_CODE, $e->getMessage());
        }  
    }
}
