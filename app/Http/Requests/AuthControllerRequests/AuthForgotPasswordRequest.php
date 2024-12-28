<?php

namespace App\Http\Requests\AuthControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class AuthForgotPasswordRequest extends FormRequest
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
            'email' => 'required|email|max:100|exists:users,email',
            'reset_form_link' => 'required|url',
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
            'email.required' => 'An email is required',
            'email.max' => 'An email can not have more than 100 characters',
            'email.email'  => 'Email is not valid',
            'email.exists' => 'Ensure that the email belongs to you',

            'reset_form_link.required' => 'A link to the reset form is required',
            'reset_form_link.url' => 'Reset form link is not valid, a url is required',
        ];
    }
}
