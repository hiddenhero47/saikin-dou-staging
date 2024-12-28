<?php

namespace App\Http\Requests\PaymentPlanControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentPlanFilterRequest extends FormRequest
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
         * Check if the user is an administrator with permission of filter_index_payment_plan
         */
        if (auth()->user()->isAbleTo('filter_index_payment_plan', 'administrator')){
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
            'name' => 'sometimes|required|string|max:50|min:1',
            'level' => 'sometimes|required|integer|min:1',
            'payment_plan_benefits' => 'sometimes|required|array|max:18|min:1',
            'payment_plan_benefits.*' => 'required_unless:payment_plan_benefits,NULL|array|max:1|min:1',
            'payment_plan_benefits.*.value' => 'required_unless:payment_plan_benefits,NULL|integer|min:1',
            'amount' => 'sometimes|required|integer|min:0',
            'discount' => 'sometimes|required|integer|min:0|lte:amount',
            'currency' => 'sometimes|required|string|max:4|min:1',
            'start_date' => 'sometimes|required|date|max:25|min:1',
            'end_date' => 'sometimes|required|date|max:25|min:1',
            'visibility' => 'sometimes|required|string|max:10|min:1',
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
            'name.sometimes' => 'A payment plan name field should be present, else entirely exclude the field',
            'name.required' => 'A payment plan name maybe required',
            'name.string'  => 'Payment plan name characters are not valid',
            'name.max'  => 'Payment plan name characters can not be more than 50',
            'name.min'  => 'Payment plan name characters can not be less than 1',

            'level.sometimes' => 'A payment plan level field should be present, else entirely exclude the field',
            'level.required' => 'A payment plan level maybe required',
            'level.string'  => 'Payment plan level characters are not valid',
            'level.min'  => 'Payment plan level characters can not be less than 1',

            'payment_plan_benefits.sometimes' => 'Payment plan benefits field should be present, else entirely exclude the field',
            'payment_plan_benefits.required' => 'Payment plan benefits field maybe required',
            'payment_plan_benefits.array'  => 'Payment plan benefits field characters are not valid, Array is expected',
            'payment_plan_benefits.max'  => 'Payment plan benefits field array can not contain more than 18 items',
            'payment_plan_benefits.min'  => 'Payment plan benefits field array can not have less than 1 items',

            'payment_plan_benefits.*.required_unless' => 'Payment plan benefits item field is required',
            'payment_plan_benefits.*.array'  => 'Payment plan benefits item field characters are not valid, Array is expected',
            'payment_plan_benefits.*.max'  => 'Payment plan benefits item field array can not contain more than 1 item',
            'payment_plan_benefits.*.min'  => 'Payment plan benefits item field array can not have less than 1 item',

            'payment_plan_benefits.*.value.required_unless' => 'Payment plan benefits item value field is required',
            'payment_plan_benefits.*.value.integer'  => 'Payment plan benefits item value field characters is not valid, Integer is expected',
            'payment_plan_benefits.*.value.min'  => 'Payment plan benefits item value field can not be less than 1',

            'amount.sometimes' => 'A amount field should be present, else entirely exclude the field',
            'amount.required' => 'A amount field maybe required',
            'amount.integer'  => 'Payment plan amount field characters are not valid, Integer is expected',
            'amount.min'  => 'Payment plan amount field can not be less than 0',

            'discount.sometimes' => 'A payment plan discount field should be present, else entirely exclude the field',
            'discount.required' => 'A payment plan discount field maybe required',
            'discount.integer'  => 'Payment plan discount field characters are not valid, Integer is expected',
            'discount.min'  => 'Payment plan discount field can not be less than 0',
            'discount.lte'  => 'Payment plan discount field can not be more than payment plan amount',

            'currency.sometimes' => 'A payment plan currency field should be present, else entirely exclude the field',
            'currency.required' => 'A payment plan currency field maybe required',
            'currency.string'  => 'Payment plan currency field characters are not valid',
            'currency.max'  => 'Payment plan currency field characters can not be more than 4',
            'currency.min'  => 'Payment plan currency field characters can not be less than 1',

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

            'visibility.sometimes' => 'A payment plan visibility field should be present, else entirely exclude the field',
            'visibility.required' => 'A payment plan visibility field maybe required',
            'visibility.string'  => 'Payment plan visibility field characters are not valid',
            'visibility.max'  => 'Payment plan visibility field characters can not be more than 10',
            'visibility.min'  => 'Payment plan visibility field characters can not be less than 1',

            'pagination.sometimes' => 'Pagination field should be present, else entirely exclude the field',
            'pagination.required' => 'Pagination field maybe required',
            'pagination.boolean'  => 'Pagination characters are not valid, Boolean is expected or leave as null'
        ];
    }
}
