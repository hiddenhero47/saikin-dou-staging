<?php

namespace App\Http\Requests\RoleControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
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
         * Check if the user is an administrator with permission of update_permission
         */
        if (auth()->user()->isAbleTo('update_role', 'administrator')) {
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
            'name' => 'sometimes|required|string|min:4|max:225|unique:roles,name',
            'description' => 'sometimes|required|string|min:4|max:225',
            'display_name' => 'sometimes|required|string|min:4|max:225',
            'visibility' => 'sometimes|required|in:private,public,protected',
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

            'name.sometimes' => 'A name field should be present, else entirely exclude the field',
            'name.required' => 'A name is required',
            'name.string'  => 'Name field characters are not valid, String is expected',
            'name.max'  => 'Name characters can not be more than 225',
            'name.min'  => 'Name characters can not be less than 4',
            'name.unique'  => 'Name already exists',

            'description.sometimes' => 'A description field should be present, else entirely exclude the field',
            'description.required' => 'A description is required',
            'description.string'  => 'Description field characters are not valid, String is expected',
            'description.max'  => 'Description characters can not be more than 225',
            'description.min'  => 'Description characters can not be less than 4',

            'display_name.sometimes' => 'A display_name field should be present, else entirely exclude the field',
            'display_name.required' => 'A display_name is required',
            'display_name.string'  => 'Display name field characters are not valid, String is expected',
            'display_name.max'  => 'Display name characters can not be more than 225',
            'display_name.min'  => 'Display name characters can not be less than 4',

            'visibility.sometimes' => 'A visibility field should be present, else entirely exclude the field',
            'visibility.required' => 'A visibility maybe required',
            'visibility.in'  => 'Visibility field characters are not valid, String of private, public or protected is expected',
        ];
    }
}
