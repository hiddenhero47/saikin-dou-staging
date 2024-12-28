<?php

namespace App\Http\Requests\ContactControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class ContactIndexRequest extends FormRequest
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

        /**
         * Check if requestor is able to ...
         * Check if the user is an administrator with permission of index_contact
         */
        if (auth()->user()->isAbleTo('index_contact', 'administrator')){
            return true;
        }

        return auth()->user()? $this->merge(['user_id' => auth()->user()->id]) : false;
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
