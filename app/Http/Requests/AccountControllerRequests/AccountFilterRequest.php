<?php

namespace App\Http\Requests\AccountControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class AccountFilterRequest extends FormRequest
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
         * Check if the user is an administrator with permission of filter_index_account
         */
        if (auth()->user()->isAbleTo('filter_index_account', 'administrator')){
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
            'phone' => 'sometimes|required|numeric|digits_between:1,25',
            'first_name' => 'sometimes|required|string|max:50|min:1',
            'last_name' => 'sometimes|required|string|max:50|min:1',
            'verified' => 'sometimes|required|boolean',
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
            'user_id.sometimes' => 'A user id should be present, else entirely exclude the field',
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

            'phone.sometimes' => 'A phone number should be present, else entirely exclude the field',
            'phone.required' => 'A phone number maybe required',
            'phone.numeric' => 'Phone number characters are not valid',
            'phone.digits_between' => 'A phone number can not have more than 25 characters or less than 1',

            'first_name.sometimes' => 'A first name should be present, else entirely exclude the field',
            'first_name.required' => 'A first name maybe required',
            'first_name.string'  => 'First name characters are not valid',
            'first_name.max'  => 'First name characters can not be more than 50',
            'first_name.min'  => 'First name characters can not be less than 1',

            'last_name.sometimes' => 'A Last name should be present, else entirely exclude the field',
            'last_name.required' => 'A Last name maybe required',
            'last_name.string'  => 'Last name characters are not valid',
            'last_name.max'  => 'Last name characters can not be more than 50',
            'last_name.min'  => 'Last name characters can not be less than 1',

            'verified.sometimes' => 'A verified field should be present, else entirely exclude the field',
            'verified.required' => 'A verified field maybe required',
            'verified.boolean'  => 'Verified field characters are not valid, Boolean is expected',

            'pagination.sometimes' => 'Pagination field should be present, else entirely exclude the field',
            'pagination.required' => 'Pagination field maybe required',
            'pagination.boolean'  => 'Pagination characters are not valid, Boolean is expected or leave as null'
        ];
    }
}
