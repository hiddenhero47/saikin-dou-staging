<?php

namespace App\Http\Requests\PublicEmbeddedFormControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PublicEmbeddedFormCustomUrlRequest extends FormRequest
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
            'custom_url' => 'required|string|min:1',
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
            'custom_url.required' => 'An custom url is required',
            'custom_url.string'  => 'Custom url characters are not valid, String is expected',
            'custom_url.min'  => 'Custom url characters can not be less than 1',
        ];
    }
}
