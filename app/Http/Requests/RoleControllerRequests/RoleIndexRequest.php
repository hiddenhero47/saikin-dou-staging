<?php

namespace App\Http\Requests\RoleControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class RoleIndexRequest extends FormRequest
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
         * Check if the user is an administrator with permission of index_role
         */
        if (auth()->user()->isAbleTo('index_role', 'administrator')){
            return true;
        }

        /**
         * Check if requestor is able to ...
         * Check if the user is a manager with permission of index_role
         */
        if (auth()->user()->isAbleTo('index_role', 'management')){
            return true;
        }

        /**
         * Check if requestor is able to ...
         * Check if the user is in a team with permission of index_role
         */
        if (auth()->user()->isAbleTo('index_role', $this->header('Team'))){
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
        ];
    }
}
