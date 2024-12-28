<?php

namespace App\Http\Requests\BrowserControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BrowserFilterRequest extends FormRequest
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
         * Check if the user is an administrator with permission of filter_index_browser
         */
        if (auth()->user()->isAbleTo('filter_index_browser', 'administrator')){
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
            'user_id' => 'sometimes|required|uuid|max:100|min:1',
            'account_id' => 'sometimes|required|integer|min:1',
            'session_id' => 'sometimes|required|string|max:100|min:1',
            'status' => 'sometimes|required|in:open,close,idle',
            'start_date' => 'sometimes|required|date|max:25|min:1',
            'end_date' => 'sometimes|required|date|max:25|min:1',
            'pagination' => 'sometimes|required|nullable|boolean'
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
            'user_id.sometimes' => 'A user id field should be present, else entirely exclude the field',
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

            'account_id.sometimes' => 'An account id field should be present, else entirely exclude the field',
            'account_id.required' => 'An account id maybe required',
            'account_id.integer'  => 'Account id characters are not valid, Integer is expected',
            'account_id.min'  => 'Account id can not be less than 1',

            'session_id.sometimes' => 'A session id field should be present, else entirely exclude the field',
            'session_id.required' => 'A session id maybe required',
            'session_id.string'  => 'Session id characters are not valid, String is expected',
            'session_id.max'  => 'Session id characters can not be more than 100',
            'session_id.min'  => 'Session id characters can not be less than 1',

            'status.sometimes' => 'A status field should be present, else entirely exclude the field',
            'status.required' => 'A status maybe required',
            'status.in'  => 'Status field characters are not valid, string of open, close or idle is expected',

            'start_date.sometimes' => 'A start date field should be present, else entirely exclude the field',
            'start_date.required' => 'A start date field maybe required',
            'start_date.date'  => 'Start date field characters are not valid, A valid date string is expected',
            'start_date.max'  => 'Start date field characters can not be more than 25',
            'start_date.min'  => 'Start date field characters can not be less than 1',

            'end_date.sometimes' => 'An end date field should be present, else entirely exclude the field',
            'end_date.required' => 'An end date field maybe required',
            'end_date.date'  => 'End date field characters are not valid, A valid date string is expected',
            'end_date.max'  => 'End date field characters can not be more than 25',
            'end_date.min'  => 'End date field characters can not be less than 1',

            'pagination.sometimes' => 'Pagination field should be present, else entirely exclude the field',
            'pagination.required' => 'Pagination field maybe required',
            'pagination.boolean'  => 'Pagination characters are not valid, Boolean is expected or leave as null'
        ];
    }
}
