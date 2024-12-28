<?php

namespace App\Http\Requests\BroadcastControllerRequests;

use App\Models\Broadcast;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BroadcastShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Find the supplied broadcast by id
        $this->broadcast = Broadcast::find($this->input('id'));

        if ($this->broadcast) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of show_broadcast
             */
            if (auth()->user()->isAbleTo('show_broadcast', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the supplied broadcast id
             */
            if (auth()->user()->id === $this->broadcast->user_id){
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
            'id' => 'required|uuid|max:100|min:1',
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
            'id.uuid'  => 'Id characters are not valid, UUID is expected',
            'id.max'  => 'Id characters can not be more than 100',
            'id.min'  => 'Id characters can not be less than 1',
        ];
    }
}
