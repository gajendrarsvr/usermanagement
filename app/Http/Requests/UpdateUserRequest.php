<?php

namespace App\Http\Requests;

use App\Models\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        // return Gate::allows('user_edit');
        return true;
    }

    public function rules()
    {
        
        return [
            'email' => [
                'required',
                'unique:users,email,'. request()->route('user'),
            ],
            // 'roles.*' => [
            //     'integer',
            // ],
            // 'roles' => [
            //     'required',
            //     'array',
            // ],
        ];
    }
}
