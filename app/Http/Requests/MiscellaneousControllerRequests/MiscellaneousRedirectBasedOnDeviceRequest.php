<?php

namespace App\Http\Requests\MiscellaneousControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class MiscellaneousRedirectBasedOnDeviceRequest extends FormRequest
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
            'channel' => 'required|string'
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
            'channel.required' => 'A channel is required',
            'channel.string'  => 'Channel characters are not valid, String is expected',
        ];
    }
}