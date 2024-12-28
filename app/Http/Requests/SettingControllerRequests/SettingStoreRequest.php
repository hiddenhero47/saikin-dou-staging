<?php

namespace App\Http\Requests\SettingControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class SettingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'messages_before_pause' => 'sometimes|required|integer|min:1',
            'minutes_before_resume' => 'sometimes|required|date_format:H:i:s',
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
            'messages_before_pause.sometimes' => 'A message before pause field should be present, else entirely exclude the field',
            'messages_before_pause.required' => 'A message before pause field maybe required',
            'messages_before_pause.integer' => 'Message before pause field characters are not valid, Integer is expected',
            'messages_before_pause.min' => 'Message before pause field characters can not be less than 1',

            'minutes_before_resume.sometimes' => 'A minutes before resuming field should be present, else entirely exclude the field',
            'minutes_before_resume.required' => 'A minutes before resuming field maybe required',
            'minutes_before_resume.date_format' => 'Minutes before resuming field characters are not valid, Time string is expected',
        ];
    }
}
