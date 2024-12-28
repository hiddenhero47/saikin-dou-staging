<?php

namespace App\Http\Requests\BrowserControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BrowserStoreRequest extends FormRequest
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
         * Check if the user is an administrator with permission of store_browser
         */
        if (auth()->user()->isAbleTo('store_browser', 'administrator')){
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
            'user_id' => 'required|uuid|max:100|min:1',
            'account_id' => 'required|integer|min:1|exists:accounts,id,user_id,'.$this->input('user_id'),
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

            'account_id.required' => 'An account id is required',
            'account_id.integer'  => 'Account id characters are not valid, Integer is expected',
            'account_id.min'  => 'Account id can not be less than 1',
            'account_id.exists'  => 'Account does not exist or does not match the user id',
        ];
    }
}
