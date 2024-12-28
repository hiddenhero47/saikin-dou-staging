<?php

namespace App\Http\Requests\BroadcastControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastSearchRequest extends FormRequest
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
         * Check if the user is a manager with permission of search_index_broadcast
         */
        if (auth()->user()->isAbleTo('search_index_broadcast', 'administrator')){
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
            'user_id' => 'sometimes|required|uuid|max:100|min:1',
            'account_id' => 'sometimes|required|integer|min:1',
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

            'user_id.sometimes' => 'A user id field should be present, else entirely exclude the field',
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

            'account_id.sometimes' => 'A account id field should be present, else entirely exclude the field',
            'account_id.required' => 'A account id maybe required',
            'account_id.integer'  => 'Account id characters are not valid, Integer is expected',
            'account_id.min'  => 'Account id characters can not be less than 1',
        ];
    }
}
