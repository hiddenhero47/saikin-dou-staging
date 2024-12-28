<?php

namespace App\Http\Requests\GroupControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class GroupIndexRequest extends FormRequest
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
         * if user is an administrator then with the permission of index_group
         */
        if (auth()->user()->isAbleTo('index_group', 'administrator')){
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
            'deleted' => 'nullable|boolean',
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
            'deleted.boolean'  => 'Deleted characters are not valid, Boolean is expected or leave as null',
        ];
    }
}
