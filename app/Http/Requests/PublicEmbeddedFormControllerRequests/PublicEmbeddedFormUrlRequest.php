<?php

namespace App\Http\Requests\PublicEmbeddedFormControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PublicEmbeddedFormUrlRequest extends FormRequest
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
            'properties' => 'nullable|boolean',
            'form_url' => 'required|string|min:1',
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
            'properties.boolean'  => 'Properties characters are not valid, Boolean is expected or leave as null',
            'form_url.required' => 'An form url is required',
            'form_url.string'  => 'Form url characters are not valid, String is expected',
            'form_url.min'  => 'Form url characters can not be less than 1',
        ];
    }
}
