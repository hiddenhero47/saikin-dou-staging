<?php

namespace App\Http\Requests\RoleControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class RoleAssignRequest extends FormRequest
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
         * Check if the user is an administrator with permission of assign_role
         */
        if ($this->header('Team') === 'administrator' && auth()->user()->isAbleTo('assign_role', 'administrator')) {
            return true;
        }

        /**
         * Check if requestor is able to ...
         * Check if the user is a manager with permission of assign_role
         */
        if ($this->header('Team') === 'management' && auth()->user()->isAbleTo('assign_role', 'management')) {
            return $this->merge(['team_name' => $this->header('Team')]);
        }

        /**
         * Check if requestor is able to ...
         * Check if the user is in a team with permission of assign_permission
         */
        if (auth()->user()->isAbleTo('assign_role', $this->header('Team'))) {
            return $this->merge(['team_name' => $this->header('Team')]);
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
            'user_id' => 'required|uuid|max:100|min:1',
            'role_id' => 'required|integer',
            'team_name' => 'sometimes|required|string|max:100|min:1',
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
            'user_id.required' => 'A user id is required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

            'role_id.required' => 'A role id is required',
            'role_id.integer'  => 'Role id characters are not valid, Integer is expected',

            'team_name.sometimes' => 'A team name field should be present, else entirely exclude the field',
            'team_name.required' => 'A team name field maybe required',
            'team_name.string'  => 'Team name field characters are not valid',
            'team_name.max'  => 'Team name field characters can not be more than 100',
            'team_name.min'  => 'Team name field characters can not be less than 1',
        ];
    }
}
