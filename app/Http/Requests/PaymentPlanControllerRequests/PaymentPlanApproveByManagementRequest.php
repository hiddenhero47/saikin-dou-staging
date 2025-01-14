<?php

namespace App\Http\Requests\PaymentPlanControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentPlanApproveByManagementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Check if requestor is able to ...
         * Check if the user is an administrator with permission of approve_payment_plan_as_management
         */
        if (auth()->user()->isAbleTo('approve_payment_plan_as_management', 'administrator')) {
            return true;
        }

        /**
         * Check if requestor is able to ...
         * Check if the user is a manger with permission of approve_payment_plan_as_management
         */
        if (auth()->user()->isAbleTo('approve_payment_plan_as_management', 'management')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1',
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
            'id.required' => 'An id is required',
            'id.integer'  => 'Id characters are not valid, Integer is expected',
            'id.min'  => 'Id characters can not be less than 1'
        ];
    }
}
