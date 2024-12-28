<?php

namespace App\Http\Requests\PaymentControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentSearchRequest extends FormRequest
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
        if (auth()->user()->isAbleTo('search_index_payment', 'administrator')){
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
            'search' => 'required|string|max:100|min:3',
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
            'search.required' => 'A search value is required',
            'search.search'  => 'Search characters are not valid',
            'search.max'  => 'Search characters can not be more than 100',
            'search.min'  => 'Search characters can not be less than 3',
        ];
    }
}
