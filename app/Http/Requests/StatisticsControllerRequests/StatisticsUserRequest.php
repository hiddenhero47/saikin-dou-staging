<?php

namespace App\Http\Requests\StatisticsControllerRequests;

use App\Rules\DateDifference;
use Illuminate\Foundation\Http\FormRequest;

class StatisticsUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Check if the user is an administrator with permission of user_statistics
         */
        if (auth()->user()->isAbleTo('user_statistics', 'administrator')){
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
            'user_id' => 'sometimes|required|uuid|max:100|min:1|exists:users,id',
            'start_date' => ['required','date','max:25','min:1', new DateDifference(['compared_with'=>'end_date','max_difference'=>'4 months'])],
            'end_date' => 'required|date|max:25|min:1',
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
            'user_id.sometimes' => 'A user id field should be present, else entirely exclude the field',
            'user_id.required' => 'An user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',
            'user_id.exists'  => 'User does not exist',

            'start_date.required' => 'A start date field is required',
            'start_date.date'  => 'Start date field characters are not valid, A valid date string is expected',
            'start_date.max'  => 'Start date field characters can not be more than 25',
            'start_date.min'  => 'Start date field characters can not be less than 1',

            'end_date.required' => 'An end date field is required',
            'end_date.date'  => 'End date field characters are not valid, A valid date string is expected',
            'end_date.max'  => 'End date field characters can not be more than 25',
            'end_date.min'  => 'End date field characters can not be less than 1',
        ];
    }
}
