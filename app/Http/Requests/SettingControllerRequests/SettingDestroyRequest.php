<?php

namespace App\Http\Requests\SettingControllerRequests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingDestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Check if the user is logged in and is part of a team through group
         */
        $group = auth()->user()->group($this->header('Team'));

        // Find the supplied user by id
        $this->setting = Setting::find($this->input('user_id'));

        if ($this->setting) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of delete_setting
             * Check if the user is related to the found setting
             */
            if (auth()->user()->isAbleTo('delete_setting', 'administrator')){
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
            'user_id.min'  => 'Id characters can not be less than 1',
        ];
    }
}
