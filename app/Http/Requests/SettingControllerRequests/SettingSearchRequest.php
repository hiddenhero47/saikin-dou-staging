<?php

namespace App\Http\Requests\SettingControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class SettingSearchRequest extends FormRequest
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
         * Check if the user is an administrator with permission of search_index_setting
         */
        if (auth()->user()->isAbleTo('search_index_setting', 'administrator')){
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
            'search' => 'required|string|max:100|min:3',
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
            'search.required' => 'A search value is required',
            'search.search'  => 'Search characters are not valid',
            'search.max'  => 'Search characters can not be more than 100',
            'search.min'  => 'Search characters can not be less than 3',
        ];
    }
}
