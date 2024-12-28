<?php

namespace App\Http\Requests\GroupControllerRequests;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Find the supplied group by id
        $this->group = Group::find($this->input('id'));

        if ($this->group) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of show_group
             */
            if (auth()->user()->isAbleTo('show_group', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found group
             */
            if ($this->group->user_id === auth()->user()->id){
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
            'id' => 'required|integer|min:1',
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

            'id.required' => 'An id is required',
            'id.integer'  => 'Id characters are not valid, Integer is expected',
            'id.min'  => 'Id characters can not be less than 1',
        ];
    }
}
