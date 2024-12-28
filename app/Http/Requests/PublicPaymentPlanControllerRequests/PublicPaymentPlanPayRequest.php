<?php

namespace App\Http\Requests\PublicPaymentPlanControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PublicPaymentPlanPayRequest extends FormRequest
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
            'properties' => 'nullable|boolean',
            'id' => 'required|integer|min:1|exists:payment_plans,id',
            'account_id' => 'required|integer|min:1|exists:accounts,id,user_id,'.auth()->user()->id,
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'properties.boolean'  => 'Properties characters are not valid, Boolean is expected or leave as null',

            'id.required' => 'An id is required',
            'id.integer'  => 'Id characters are not valid, Integer is expected',
            'id.min'  => 'Id characters can not be less than 1',
            'id.exists'  => 'The payment plan does not exist',

            'account_id.required' => 'Account id is required',
            'account_id.integer'  => 'Account id characters are not valid, Integer is expected',
            'account_id.min'  => 'Account id can not be less than 1',
            'account_id.exists'  => 'The account does not exist or does not belong to the user',
        ];
    }
}
