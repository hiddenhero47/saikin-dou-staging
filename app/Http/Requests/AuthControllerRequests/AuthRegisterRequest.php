<?php

namespace App\Http\Requests\AuthControllerRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class AuthRegisterRequest extends FormRequest
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|min:6|max:24|confirmed',
            'password_confirmation' => 'required|same:password',
            'phone' => 'sometimes|required|numeric|digits_between:1,25',
            'description' => 'string|max:225',
            'birth_date' => 'string|max:11|date_format:Y-m-d',
            'birth_year' => 'string|max:4|date_format:Y',
            'referrer_code' => 'sometimes|required|string|max:50|min:1',
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
            'first_name.required' => 'A name is required',
            'first_name.string' => 'Name characters are not valid',
            'first_name.max' => 'A name can not have more than 50 characters',

            'last_name.required' => 'A last name is required',
            'last_name.string' => 'Last name characters are not valid',
            'last_name.max' => 'A last name can not have more than 50 characters',

            'email.required' => 'An email is required',
            'email.email' => 'Email is not valid',
            'email.max' => 'An email can not have more than 100 characters',
            'email.unique' => 'The email has already been taken',

            'password.required'  => 'A password is required',
            'password.min'  => 'Password must have a minimum of 6 characters',
            'password.max'  => 'Password must have a maximum of 24 characters',

            'password.confirmed'  => 'A password confirmation is required',
            'password_confirmation.required'  => 'A password confirmation is required',
            'password_confirmation.same'  => 'Passwords do not match',

            'phone.sometimes' => 'A phone should be present, else entirely exclude the field',
            'phone.required' => 'A phone number maybe required',
            'phone.numeric' => 'Phone number characters are not valid',
            'phone.digits_between' => 'A phone number can not have more than 25 characters or less than 1',

            'description.max' => 'A Description can not have more than 225 characters',
            'description.string' => 'Description characters are not valid',

            'birth_date.max' => 'Birth date can not have more than 25 characters',
            'birth_date.string' => 'Birth date characters are not valid',
            'birth_date.date_format' => 'Birth date is not valid',

            'birth_year.max' => 'Birth year can not have more than 4 characters',
            'birth_year.string' => 'Birth year characters are not valid',
            'birth_year.date_format' => 'Birth year is not valid',

            'referrer_code.sometimes' => 'A referrer code should be present, else entirely exclude the field',
            'referrer_code.required' => 'A referrer code maybe required',
            'referrer_code.string'  => 'Referrer code characters are not valid',
            'referrer_code.max'  => 'Referrer code characters can not be more than 50',
            'referrer_code.min'  => 'Referrer code characters can not be less than 1',
        ];
    }
}
