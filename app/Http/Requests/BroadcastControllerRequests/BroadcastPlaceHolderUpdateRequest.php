<?php

namespace App\Http\Requests\BroadcastControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastPlaceHolderUpdateRequest extends FormRequest
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
         * Check if the user is an administrator with permission of store_application_setting
         */
        if (auth()->user()->isAbleTo('broadcast_placeholder_update', 'administrator')){
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
            'placeholders' => 'required|array|min:1|filled',
            'placeholders.*' => 'required_unless:placeholders,'.null.'|string|min:1',
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
            'placeholders.sometimes' => 'Placeholders field should be present, else entirely exclude the field',
            'placeholders.required' => 'An array of placeholders maybe required',
            'placeholders.array' => 'The placeholders field is not valid, Array is expected',
            'placeholders.min' => 'Placeholders array can not contain less than one(1) item',
            'placeholders.filled' => 'Placeholders array can not be empty',

            'placeholders.*.string' => 'Placeholders item must be a string',
            'placeholders.*.min' => 'Placeholders item character length can not be less than one(1)',
        ];
    }
}
