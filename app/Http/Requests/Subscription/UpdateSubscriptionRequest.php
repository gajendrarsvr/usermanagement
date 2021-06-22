<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'app_id'=>'required|exists:apps,id',
            'subscription_plan' =>'required|exists:subscription_plans,id',
            'id'=>'exists:subscriptions,'.request()->route('subscription')
         ];
    }
}
