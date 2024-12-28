<?php

namespace App\Http\Requests\BenefitControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class BenefitUpdateRequest extends FormRequest
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
         * Check if the user is an administrator with benefit of update_benefit
         */
        if (auth()->user()->isAbleTo('update_benefit', 'administrator')) {
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
            'benefit_id' => 'required|integer',
            'name' => 'required|string|min:4|max:225',
            'display_name' => 'required|string|min:4|max:225',
            'description' => 'required|string|min:4|max:225',
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
            'benefit_id.required' => 'An benefit id is required',
            'benefit_id.integer'  => 'Benefit id characters are not valid, Integer is expected',

            'name.required' => 'A name is required',
            'name.string'  => 'Name field characters are not valid, String is expected',
            'name.max'  => 'Name characters can not be more than 225',
            'name.min'  => 'Name characters can not be less than 4',

            'display_name.required' => 'A display name is required',
            'display_name.string'  => 'Display name field characters are not valid, String is expected',
            'display_name.max'  => 'Display name characters can not be more than 225',
            'display_name.min'  => 'Display name characters can not be less than 4',

            'description.required' => 'A description is required',
            'description.string'  => 'Description field characters are not valid, String is expected',
            'description.max'  => 'Description characters can not be more than 225',
            'description.min'  => 'Description characters can not be less than 4',
        ];
    }
}
