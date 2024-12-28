<?php

namespace App\Http\Requests\RoleControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class RoleShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Check if requestor is able to ...
         * Check if the user is an administrator with permission of show_permission
         */
        if (auth()->user()->isAbleTo('show_role', 'administrator')){
            return true;
        }

        /**
         * Check if requestor is able to ...
         * Check if the user is a manager with permission of show_permission
         */
        if (auth()->user()->isAbleTo('show_role', 'management')){
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
            'properties' => 'nullable|boolean',
            'role_id' => 'required|integer',
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

            'role_id.required' => 'A role id is required',
            'role_id.integer'  => 'Role id characters are not valid, Integer is expected',
        ];
    }
}
