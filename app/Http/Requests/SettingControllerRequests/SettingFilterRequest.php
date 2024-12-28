<?php

namespace App\Http\Requests\SettingControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class SettingFilterRequest extends FormRequest
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
         * Check if the user is an administrator with permission of filter_index_setting
         */
        if (auth()->user()->isAbleTo('filter_index_setting', 'administrator')){
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
            'messages_before_pause' => 'sometimes|required|integer|min:1',
            'minutes_before_resume' => 'sometimes|required|date_format:H:i:s',
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
            'user_id.uuid' => 'User id characters are not valid, UUID is expected',
            'user_id.max' => 'User id characters can not be more than 100',
            'user_id.min' => 'User id characters can not be less than 1',

            'messages_before_pause.sometimes' => 'A message before pause field should be present, else entirely exclude the field',
            'messages_before_pause.required' => 'A message before pause field maybe required',
            'messages_before_pause.integer' => 'Message before pause field characters are not valid, Integer is expected',
            'messages_before_pause.min' => 'Message before pause field characters can not be less than 1',

            'minutes_before_resume.sometimes' => 'A minutes before resuming field should be present, else entirely exclude the field',
            'minutes_before_resume.required' => 'A minutes before resuming field maybe required',
            'minutes_before_resume.date_format' => 'Minutes before resuming field characters are not valid, Time string is expected',

            'pagination.sometimes' => 'Pagination field should be present, else entirely exclude the field',
            'pagination.required' => 'Pagination field maybe required',
            'pagination.boolean' => 'Pagination characters are not valid, Boolean is expected or leave as null'
        ];
    }
}
