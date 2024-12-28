<?php

namespace App\Http\Requests\PermissionControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionShowRequest extends FormRequest
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
         * Check if the user is an administrator with permission of show_permission
         */
        if (!auth()->user()->isAbleTo('show_permission', 'administrator')){
            return false;
        }

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
            'permission_id' => 'required|integer',
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

            'permission_id.required' => 'A permission id is required',
            'permission_id.integer'  => 'Permission id characters are not valid, Integer is expected',
        ];
    }
}
