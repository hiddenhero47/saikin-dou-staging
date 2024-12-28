<?php

namespace App\Http\Requests\SocialiteControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class SocialiteAppleCallBackRequest extends FormRequest
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
            'code' => 'required_without:token|string',
            'token' => 'required_without:code|string',
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
            'code.required_without' => 'A code or authorization token is required is required',
            'code.string'  => 'Code field characters are not valid',

            'token.required_without' => 'An authorization token or code is required is required',
            'token.string'  => 'Token field characters are not valid',
        ];
    }
}
