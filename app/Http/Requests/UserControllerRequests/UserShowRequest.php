<?php

namespace App\Http\Requests\UserControllerRequests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserShowRequest extends FormRequest
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
        $this->user = User::find($this->input('user_id'));

        if ($this->user) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of show_user
             * Check if the user is related to the found user
             */
            if (auth()->user()->isAbleTo('show_user', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found user
             */
            if ($this->user->id === auth()->user()->id){
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
            'properties' => 'nullable|boolean',
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
            'properties.boolean'  => 'Properties characters are not valid, Boolean is expected or leave as null',

            'user_id.required' => 'A user id is required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',
        ];
    }
}
