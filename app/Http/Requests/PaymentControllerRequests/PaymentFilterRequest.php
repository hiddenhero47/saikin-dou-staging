<?php

namespace App\Http\Requests\PaymentControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentFilterRequest extends FormRequest
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
        if (auth()->user()->isAbleTo('filter_index_payment', 'administrator')){
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
            'user_id' => 'sometimes|required|uuid|max:100|min:1',
            'type' => 'sometimes|required|in:standard,collect',
            'amount' => 'sometimes|required|integer|min:0',
            'currency' => 'sometimes|required|string|max:4|min:1',
            'start_date' => 'sometimes|required|date|max:25|min:1',
            'end_date' => 'sometimes|required|date|max:25|min:1',
            'paid' => 'sometimes|required|nullable|boolean',
            'payment_confirmed' => 'sometimes|required|nullable|boolean',
            'payment_method' => 'sometimes|required|string|min:1',
            'pagination' => 'sometimes|required|nullable|boolean'
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
            'user_id.sometimes' => 'A user id field should be present, else entirely exclude the field',
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

            'type.sometimes' => 'A payment type field should be present, else entirely exclude the field',
            'type.required' => 'Payment type maybe required',
            'type.in'  => 'Payment type field characters are not valid, string of standard or collect is expected',

            'amount.sometimes' => 'A payment amount field should be present, else entirely exclude the field',
            'amount.required' => 'A payment amount field maybe required',
            'amount.integer'  => 'Payment amount field characters are not valid, Integer is expected',
            'amount.min'  => 'Payment amount field can not be less than 0',

            'currency.sometimes' => 'A payment currency field should be present, else entirely exclude the field',
            'currency.required' => 'A payment currency field maybe required',
            'currency.string'  => 'Payment currency field characters are not valid',
            'currency.max'  => 'Payment currency field characters can not be more than 4',
            'currency.min'  => 'Payment currency field characters can not be less than 1',

            'start_date.sometimes' => 'A start date field should be present, else entirely exclude the field',
            'start_date.required' => 'A start date field maybe required',
            'start_date.date'  => 'Start date field characters are not valid, A valid date string is expected',
            'start_date.max'  => 'Start date field characters can not be more than 25',
            'start_date.min'  => 'Start date field characters can not be less than 1',

            'end_date.sometimes' => 'An end date field should be present, else entirely exclude the field',
            'end_date.required' => 'An end date field maybe required',
            'end_date.date'  => 'End date field characters are not valid, A valid date string is expected',
            'end_date.max'  => 'End date field characters can not be more than 25',
            'end_date.min'  => 'End date field characters can not be less than 1',

            'paid.sometimes' => 'Paid field should be present, else entirely exclude the field',
            'paid.required' => 'Paid field maybe required',
            'paid.boolean'  => 'Paid characters are not valid, Boolean is expected or leave as null',

            'confirmed.sometimes' => 'Payment confirmed field should be present, else entirely exclude the field',
            'confirmed.required' => 'Payment confirmed field maybe required',
            'confirmed.boolean'  => 'Payment confirmed characters are not valid, Boolean is expected or leave as null',

            'method.sometimes' => 'Payment method field should be present, else entirely exclude the field',
            'method.required' => 'A payment method details is required',
            'method.string' => 'The payment method details field is not valid, String is expected',
            'method.min' => 'Payment method character length can not be less than one(1)',

            'pagination.sometimes' => 'Pagination field should be present, else entirely exclude the field',
            'pagination.required' => 'Pagination field maybe required',
            'pagination.boolean'  => 'Pagination characters are not valid, Boolean is expected or leave as null'
        ];
    }
}
