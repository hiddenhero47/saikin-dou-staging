<?php

namespace App\Http\Requests\MiscellaneousControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class MiscellaneousResponseTypesRequest extends FormRequest
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
            'status_code' => 'required|integer|in:200,201,204,400,401,403,404,405,409,422,500,501'
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
            'status_code.required' => 'A status code is required',
            'status_code.integer'  => 'Status code characters are not valid, Integer is expected',
            'status_code.in'  => 'Supported status codes are 200,201,204,400,401,403,404,405,409,422,500,501',
        ];
    }
}