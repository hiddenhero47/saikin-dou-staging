<?php

namespace App\Http\Requests\RoleControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class RoleDestroyRequest extends FormRequest
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
         * Check if the user is an administrator with permission of delete_permission
         */
        if (auth()->user()->isAbleTo('delete_role', 'administrator')) {
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
            'role_id.required' => 'A role id is required',
            'role_id.integer'  => 'Role id characters are not valid, Integer is expected',
        ];
    }
}
