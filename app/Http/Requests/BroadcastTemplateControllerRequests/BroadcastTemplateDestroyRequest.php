<?php

namespace App\Http\Requests\BroadcastTemplateControllerRequests;

use App\Models\BroadcastTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BroadcastTemplateDestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Find the supplied broadcast template by id
        $this->broadcast_template = BroadcastTemplate::find($this->input('id'));

        if ($this->broadcast_template) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of delete_broadcast_template
             */
            if (auth()->user()->isAbleTo('delete_broadcast_template', 'administrator')) {
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
            'id.required' => 'An id is required',
            'id.uuid'  => 'Id characters are not valid, UUID is expected',
            'id.max'  => 'Id characters can not be more than 100',
            'id.min'  => 'Id characters can not be less than 1',
        ];
    }
}
