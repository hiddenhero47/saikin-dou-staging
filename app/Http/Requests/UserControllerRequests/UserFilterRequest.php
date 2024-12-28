<?php

namespace App\Http\Requests\UserControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest
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
         * Check if the user is an administrator with permission of filter_index_user
         */
        if (auth()->user()->isAbleTo('filter_index_user', 'administrator')){
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
            'email' => 'sometimes|required|email:rfc|max:100|min:1',
            'first_name' => 'sometimes|required|string|max:100|min:1',
            'last_name' => 'sometimes|required|string|max:100|min:1',
            'phone' => 'sometimes|required|numeric|digits_between:1,25',
            'gender' => 'sometimes|required|string|max:50|min:1',
            'birth_date' => 'sometimes|required|string|max:10|min:10',
            'birth_year' => 'sometimes|required|string|max:4|min:4',
            'email_verified_at' => 'sometimes|required|string|max:10|min:10',
            'start_date' => 'sometimes|required|date|max:25|min:1',
            'end_date' => 'sometimes|required|date|max:25|min:1',
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

            'email.sometimes' => 'An email should be present, else entirely exclude the field',
            'email.required' => 'An email maybe required',
            'email.email'  => 'Email characters are not valid',
            'email.max'  => 'Email characters can not be more than 100',
            'email.min'  => 'Email characters can not be less than 1',

            'first_name.sometimes' => 'A first name should be present, else entirely exclude the field',
            'first_name.required' => 'A first name maybe required',
            'first_name.string'  => 'First name characters are not valid',
            'first_name.max'  => 'First name characters can not be more than 100',
            'first_name.min'  => 'First name characters can not be less than 1',

            'last_name.sometimes' => 'A last name should be present, else entirely exclude the field',
            'last_name.required' => 'A last name maybe required',
            'last_name.string'  => 'Last name characters are not valid',
            'last_name.max'  => 'Last name characters can not be more than 100',
            'last_name.min'  => 'Last name characters can not be less than 1',

            'phone.sometimes' => 'A phone number should be present, else entirely exclude the field',
            'phone.required' => 'A phone number maybe required',
            'phone.numeric' => 'Phone number characters are not valid',
            'phone.digits_between' => 'A phone number can not have more than 25 characters or less than 1',

            'gender.sometimes' => 'A gender field should be present, else entirely exclude the field',
            'gender.required' => 'A gender field maybe required',
            'gender.string'  => 'Gender field characters are not valid',
            'gender.max'  => 'Gender field characters can not be more than 50',
            'gender.min'  => 'Gender field characters can not be less than 1',

            'birth_date.sometimes' => 'A birth date should be present, else entirely exclude the field',
            'birth_date.required' => 'A birth date maybe required',
            'birth_date.string'  => 'Birth date characters are not valid',
            'birth_date.max'  => 'Birth date characters can not be more than 10',
            'birth_date.min'  => 'Birth date characters can not be less than 10',

            'birth_year.sometimes' => 'A birth year should be present, else entirely exclude the field',
            'birth_year.required' => 'A birth year maybe required',
            'birth_year.string'  => 'Birth year characters are not valid',
            'birth_year.max'  => 'Birth year characters can not be more than 4',
            'birth_year.min'  => 'Birth year characters can not be less than 4',

            'email_verified_at.sometimes' => 'An email verification date should be present, else entirely exclude the field',
            'email_verified_at.required' => 'An email verification date maybe required',
            'email_verified_at.string'  => 'Email verification date characters are not valid',
            'email_verified_at.max'  => 'Email verification date characters can not be more than 10',
            'email_verified_at.min'  => 'Email verification date characters can not be less than 10',

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

            'pagination.sometimes' => 'Pagination field should be present, else entirely exclude the field',
            'pagination.required' => 'Pagination field maybe required',
            'pagination.boolean'  => 'Pagination characters are not valid, Boolean is expected or leave as null'
        ];
    }
}
