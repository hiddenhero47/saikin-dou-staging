<?php

namespace App\Http\Requests\SettingControllerRequests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Find the supplied user by id
        $this->setting = Setting::find($this->input('user_id'));

        if ($this->setting) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of update_setting
             */
            if (auth()->user()->isAbleTo('update_setting', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found setting
             */
            if ($this->setting->user_id === auth()->user()->id){
                return true;
            }

           return false;

        } else {

            // Return failure
            throw new NotFoundHttpException();
        }
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
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

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
