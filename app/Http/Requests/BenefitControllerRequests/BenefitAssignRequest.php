<?php

namespace App\Http\Requests\BenefitControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BenefitAssignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Check if the user is logged in and is part of a team through group
         */
        $group = auth()->user()->group($this->header('Team'));

        /**
         * Check if requestor is able to ...
         * Check if the user is an administrator with benefit of assign_benefit
         */
        if (auth()->user()->isAbleTo('assign_benefit', 'administrator')) {
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
            'benefit_id' => 'required|integer',
            'payment_plan_id' => 'required|integer',
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
            'benefit_id.required' => 'An benefit id is required',
            'benefit_id.integer'  => 'Benefit id characters are not valid, Integer is expected',

            'payment_plan_id.required' => 'A payment_plan id is required',
            'payment_plan_id.integer'  => 'Payment Plan id characters are not valid, Integer is expected',
        ];
    }
}