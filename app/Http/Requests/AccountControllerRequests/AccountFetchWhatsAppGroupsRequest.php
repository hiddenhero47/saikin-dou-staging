<?php

namespace App\Http\Requests\AccountControllerRequests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountFetchWhatsAppGroupsRequest extends FormRequest
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
        $this->account = Account::find($this->input('id'));

        if ($this->account) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of show_account
             * Check if the user is related to the found account
             */
            if (auth()->user()->isAbleTo('show_account', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found account
             */
            if ($this->account->user_id === auth()->user()->id){
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
            'id.min'  => 'Id can not be less than 1',
        ];
    }
}
