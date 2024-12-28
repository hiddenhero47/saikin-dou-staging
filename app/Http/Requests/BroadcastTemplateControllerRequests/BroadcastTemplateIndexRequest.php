<?php

namespace App\Http\Requests\BroadcastTemplateControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastTemplateIndexRequest extends FormRequest
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
         * Check if the user is a manager with permission of index_broadcast_template
         */
        if (auth()->user()->isAbleTo('index_broadcast_template', 'administrator')){
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
            'properties' => 'nullable|boolean',
            'deleted' => 'nullable|boolean',
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
            'deleted.boolean'  => 'Deleted characters are not valid, Boolean is expected or leave as null',
        ];
    }
}
