<?php

namespace App\Http\Requests\MiscellaneousControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class MiscellaneousApplicationPatcherRequest extends FormRequest
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
            'access_code' => 'required|string'
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
            'access_code.required' => 'An access code is required',
            'access_code.string'  => 'Access code characters are not valid, String is expected',
        ];
    }
}