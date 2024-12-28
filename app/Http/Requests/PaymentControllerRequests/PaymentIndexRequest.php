<?php

namespace App\Http\Requests\PaymentControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentIndexRequest extends FormRequest
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
        $business_id = auth()->user()->group($this->header('Team'),'business_id');

        /**
         * Check if requestor is able to ...
         * if user is not an administrator then over write the business_id input
         * with the users team business id in headers
         */
        if (auth()->user()->isAbleTo('index_payment', 'administrator')){
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
            'properties' => 'nullable|boolean',
            'deleted' => 'nullable|boolean',
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

            'deleted.boolean'  => 'Deleted characters are not valid, Boolean is expected or leave as null',
        ];
    }
}
