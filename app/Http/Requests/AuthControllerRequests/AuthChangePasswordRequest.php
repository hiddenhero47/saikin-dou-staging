<?php

namespace App\Http\Requests\AuthControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class AuthChangePasswordRequest extends FormRequest
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
            'old_password' => 'required',
            'new_password' => 'required|min:6|max:24|confirmed',
            'new_password_confirmation' => 'required|same:new_password',
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
            'old_password.required'  => 'Old password is required',
            'new_password.required'  => 'A new password is required',
            'new_password.min'  => 'Password must have a minimum of 6 characters',
            'new_password.max'  => 'Password must have a maximum of 24 characters',
            'new_password.confirmed'  => 'A new password confirmation is required',
            'new_password_confirmation.required'  => 'A new password confirmation is required',
            'new_password_confirmation.same'  => 'Passwords do not match',
        ];
    }
}
