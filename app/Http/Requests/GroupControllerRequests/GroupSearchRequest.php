<?php

namespace App\Http\Requests\GroupControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class GroupSearchRequest extends FormRequest
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
         * if user is an administrator with the permission of search_index_group
         */
        if (auth()->user()->isAbleTo('search_index_group', 'administrator')){
            return true;
        }

        return $this->merge(['user_id' => auth()->user()->id]);
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
