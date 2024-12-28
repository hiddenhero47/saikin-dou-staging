<?php

namespace App\Http\Requests\PermissionControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionDestroyRequest extends FormRequest
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
         * Check if the user is an administrator with permission of delete_permission
         */
        if (!auth()->user()->isAbleTo('delete_permission', 'administrator')) {
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
            'permission_id.required' => 'An permission id is required',
            'permission_id.integer'  => 'Permission id characters are not valid, Integer is expected',
        ];
    }
}
