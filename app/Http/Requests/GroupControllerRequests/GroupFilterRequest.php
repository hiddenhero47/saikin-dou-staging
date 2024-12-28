<?php

namespace App\Http\Requests\GroupControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class GroupFilterRequest extends FormRequest
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
         * if user is an administrator with the permission of filter_index_group
         */
        if (auth()->user()->isAbleTo('filter_index_group', 'administrator')){
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
            'user_id' => 'sometimes|required|uuid|max:100|min:1',
            'title' => 'sometimes|required|string|max:50|min:1',
            'group_contacts' => 'sometimes|required|array|max:9|min:1',
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

            'title.sometimes' => 'A group title field should be present, else entirely exclude the field',
            'title.required' => 'A group title maybe required',
            'title.string'  => 'Group title characters are not valid',
            'title.max'  => 'Group title characters can not be more than 50',
            'title.min'  => 'Group title characters can not be less than 1',

            'group_contacts.sometimes' => 'Group contacts field should be present, else entirely exclude the field',
            'group_contacts.required' => 'Group contacts field maybe required',
            'group_contacts.array'  => 'Group contacts field characters are not valid, Array is expected',
            'group_contacts.max'  => 'Group contacts field array can not contain more than 9 items',
            'group_contacts.min'  => 'Group contacts field array can not have less than 1 items',

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
