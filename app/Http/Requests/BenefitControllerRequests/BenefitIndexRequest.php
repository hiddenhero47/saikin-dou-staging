<?php

namespace App\Http\Requests\BenefitControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BenefitIndexRequest extends FormRequest
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
         * Check if the user is an administrator with benefit of index_benefit
         */
        if (auth()->user()->isAbleTo('index_benefit', 'administrator')){
            return true;
        }

        return false;
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
        ];
    }
}
