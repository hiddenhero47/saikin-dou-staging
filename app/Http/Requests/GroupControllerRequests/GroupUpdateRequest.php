<?php

namespace App\Http\Requests\GroupControllerRequests;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupUpdateRequest extends FormRequest
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
             * Check if the user is an administrator with permission of update_group
             */
            if (auth()->user()->isAbleTo('update_group', 'administrator')){
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
            'id' => 'required|integer|min:1',
            'title' => 'sometimes|required|string|max:50|min:1',
            'group_contacts' => 'sometimes|required|array|min:1',
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
            'id.required' => 'An id is required',
            'id.integer'  => 'Id characters are not valid, Integer is expected',
            'id.min'  => 'Id characters can not be less than 1',

            'title.sometimes' => 'A group title field should be present, else entirely exclude the field',
            'title.required' => 'A group title maybe required',
            'title.string'  => 'Group title characters are not valid',
            'title.max'  => 'Group title characters can not be more than 50',
            'title.min'  => 'Group title characters can not be less than 1',

            'group_contacts.sometimes' => 'Group contacts field should be present, else entirely exclude the field',
            'group_contacts.required' => 'Group contacts field maybe required',
            'group_contacts.array'  => 'Group contacts field characters are not valid, Array is expected',
            'group_contacts.min'  => 'Group contacts field array can not have less than 1 items',
        ];
    }
}
