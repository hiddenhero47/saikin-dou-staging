<?php

namespace App\Http\Requests\EmbeddedFormControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class EmbeddedFormFilterRequest extends FormRequest
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
         * Check if the user is an administrator with permission of filter_index_embedded_form
         */
        if (auth()->user()->isAbleTo('filter_index_embedded_form', 'administrator')){
            return true;
        }

        return auth()->user()? $this->merge(['user_id' => auth()->user()->id]) : false;
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
            'group_id' => 'sometimes|required|integer|min:1',
            'title' => 'sometimes|required|string|max:100',
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
            'user_id.sometimes' => 'A user id should be present, else entirely exclude the field',
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

            'group_id.sometimes' => 'A group id should be present, else entirely exclude the field',
            'group_id.required' => 'A group id maybe required',
            'group_id.integer'  => 'Group id characters are not valid, Integer is expected',
            'group_id.min'  => 'Group id characters can not be less than 1',

            'title.sometimes' => 'A title field should be present, else entirely exclude the field',
            'title.required' => 'A title field maybe required',
            'title.string'  => 'Title field characters are not valid',
            'title.max'  => 'Title characters can not be more than 100',

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
