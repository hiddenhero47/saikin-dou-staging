<?php

namespace App\Http\Requests\CacheControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class CacheClearRequest extends FormRequest
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
         * Check if the user is an administrator with permission of index_cache
         */
        if (!auth()->user()->isAbleTo('clear_cache', 'administrator')){
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
            'clear_list' => 'required|array|min:1|filled',
            'clear_list.*' => 'required_unless:clear_list,'.null.'|string|max:100|min:1',
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
            'clear_list.required' => 'Clear list field is required',
            'clear_list.array' => 'The clear list field is not valid, Array is expected',
            'clear_list.min' => 'Clear list array must contain at least one item',
            'clear_list.filled' => 'Clear list array can not be empty',

            'clear_list.*.string'  => 'A clear list item characters are not valid',
            'clear_list.*.max'  => 'A clear list item characters can not be more than 100',
            'clear_list.*.min'  => 'A clear list item characters can not be less than 1',

        ];
    }
}
